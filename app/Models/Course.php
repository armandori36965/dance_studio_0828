<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use App\Traits\ClearsCalendarCache;
use App\Models\SchoolEvent;

class Course extends Model
{
    use ClearsCalendarCache;
    protected $fillable = [
        'name', 'description', 'price', 'start_time', 'end_time', 'student_count', 'campus_id',
        'teacher_id', 'level', 'is_active', 'sort_order', 'is_weekly_course', 'total_sessions',
        'weekdays', 'avoid_school_events', 'avoid_event_types', 'pricing_type',  // 新增
    ];

    // 週期課程的 start_time 和 end_time 不儲存到資料庫，僅用於排定條件
    protected $hidden = [];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'is_weekly_course' => 'boolean',
        'total_sessions' => 'integer',
        'weekdays' => 'array',
        'avoid_school_events' => 'boolean',
        'avoid_event_types' => 'array',
        'pricing_type' => 'string',
    ];

    // 與校區的關係
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    // 與老師的關係
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // 與學生的多對多關係
    public function students()
    {
        return $this->belongsToMany(User::class, 'user_course');
    }

    // 與課程排定日期的關係
    public function sessions(): HasMany
    {
        return $this->hasMany(CourseSession::class);
    }

    // 新增: 計算總價格
    public function getTotalPrice(): float
    {
        if ($this->pricing_type === 'per_session') {
            return $this->price * ($this->total_sessions ?? $this->sessions()->count());  // 每堂計算
        } elseif ($this->pricing_type === 'per_student') {
            $studentCount = $this->students()->count();  // 依報名人數計算
            return $this->price * $studentCount;
        }
        return 0;  // 預設
    }

    /**
     * 模型啟動事件
     */
    protected static function boot()
    {
        parent::boot();

        // 當課程被創建後，自動生成課程排定並清除快取
        static::created(function ($course) {
            $course->generateCourseSessions();
            $course->clearCalendarCache();
        });

        // 當課程被更新後，重新生成課程排定
        static::updated(function ($course) {
            $relevantFields = [
                'is_weekly_course', 'weekdays', 'total_sessions', 'campus_id', 'teacher_id',
                'is_active', 'avoid_school_events', 'avoid_event_types', 'pricing_type',
            ];

            if ($course->wasChanged($relevantFields)) {
                Log::info('Course updated, regenerating sessions for course: ' . $course->id);
                // 先刪除舊的排定
                $course->sessions()->delete();
                // 重新生成課程排定（會自動判斷是否使用隊列）
                $course->generateCourseSessions();
            }

            // 清除行事曆快取
            $course->clearCalendarCache();
        });

        // 當課程被刪除後，清除行事曆快取
        static::deleted(function ($course) {
            $course->clearCalendarCache();
        });

        // 模型啟動事件
        static::saving(function ($course) {
            Log::info('Course saving', [
                'course_id' => $course->id,
                'is_weekly_course' => $course->is_weekly_course,
                'start_time' => $course->start_time?->toDateTimeString(),
                'end_time' => $course->end_time?->toDateTimeString(),
            ]);

            // 驗證邏輯 - 放寬條件
            if ($course->is_weekly_course) {
                // 週期課程需要 weekdays 和 total_sessions，但允許在後續步驟中設定
                if (empty($course->weekdays)) {
                    Log::warning("週期課程 {$course->id} 缺少 weekdays，將使用預設值");
                    $course->weekdays = ['1']; // 預設星期一
                }
                if (!$course->total_sessions || $course->total_sessions <= 0) {
                    Log::warning("週期課程 {$course->id} 缺少 total_sessions，將使用預設值");
                    $course->total_sessions = 1; // 預設1堂
                }
            }

            // 時間驗證
            if ($course->start_time && $course->end_time && $course->end_time->lte($course->start_time)) {
                throw new \Exception('結束時間必須晚於開始時間');
            }
        });
    }

    /**
     * 獲取路由鍵名
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    // Course 不再直接實現 Eventable 接口
    // 日曆事件完全由 CourseSession 處理

    /**
     * 生成課程排定
     */
    public function generateCourseSessions(): void
    {
        // 先檢查是否已經有課程排定，避免重複生成
        if ($this->sessions()->exists()) {
            Log::info('Course sessions already exist for course: ' . $this->id);
            return;
        }

        // 如果總堂數大於閾值，使用隊列處理避免 UI 延遲
        if ($this->total_sessions > 500) {
            \App\Jobs\GenerateCourseSessions::dispatch($this);
            Log::info("Dispatched queue job for large course generation: {$this->id} ({$this->total_sessions} sessions)");
            return;
        }

        if ($this->is_weekly_course && $this->weekdays && $this->total_sessions) {
            // 生成週期課程的排定
            $this->generateWeeklyCourseSessions();
        } else {
            // 生成單次課程的排定
            $this->generateSingleCourseSession();
        }
    }

    // 批量生成週期課程排定（從 start_time 分離日期作為起始，避開事件）
    protected function generateWeeklyCourseSessions(): void
    {
        // 檢查必要資料，如果缺少則使用預設值
        if (!$this->start_time) {
            Log::warning("Course {$this->id} missing start_time, using current time");
            $this->start_time = now();
        }

        if (empty($this->weekdays)) {
            Log::warning("Course {$this->id} missing weekdays, using Monday");
            $this->weekdays = ['1'];
        }

        if (!$this->total_sessions || $this->total_sessions <= 0) {
            Log::warning("Course {$this->id} missing total_sessions, using 1");
            $this->total_sessions = 1;
        }

        // 分離起始日期（從 start_time 取日期部分，允許調整任意日期）
        $startDate = $this->start_time->copy()->startOfDay();
        $maxDate = $startDate->copy()->addYear();  // 限制範圍

        // 預載校務事件
        $schoolEvents = SchoolEvent::where('start_time', '>=', $startDate)
            ->where('end_time', '<=', $maxDate)
            ->get();

        // 調試日志：顯示載入的校務事件
        Log::info("Course {$this->id}: Loaded " . $schoolEvents->count() . " school events for date range {$startDate->format('Y-m-d')} to {$maxDate->format('Y-m-d')}");
        Log::info("Course {$this->id}: Avoid school events = " . ($this->avoid_school_events ? 'true' : 'false'));
        Log::info("Course {$this->id}: Avoid event types = " . json_encode($this->avoid_event_types ?? []));

        $schoolEvents = $schoolEvents->groupBy(fn($event) => $event->start_time->format('Y-m-d'));

        // 生成並過濾有效日期
        $period = CarbonPeriod::create($startDate, '1 day', $maxDate);
        $validDates = collect($period)
            ->filter(fn($date) => in_array((string)$date->dayOfWeek, $this->weekdays))
            ->filter(function ($date) use ($schoolEvents) {
                $dateKey = $date->format('Y-m-d');
                $events = $schoolEvents->get($dateKey, collect());

                // 如果不避開校務事件，保留此日期
                if (!$this->avoid_school_events) {
                    Log::debug("Course {$this->id}: {$dateKey} - Not avoiding school events, keeping date");
                    return true;
                }

                // 如果這個日期沒有校務事件，保留此日期
                if ($events->isEmpty()) {
                    Log::debug("Course {$this->id}: {$dateKey} - No school events, keeping date");
                    return true;
                }

                // 有校務事件，記錄詳細資訊
                $eventDetails = $events->map(fn($e) => $e->category . ':' . $e->title)->join(', ');
                Log::info("Course {$this->id}: {$dateKey} - Found events: {$eventDetails}");

                // 如果沒有指定要避開的事件類型，避開所有校務事件
                if (empty($this->avoid_event_types)) {
                    Log::info("Course {$this->id}: {$dateKey} - No specific event types to avoid, skipping all events");
                    return false;
                }

                // 檢查是否有需要避開的事件類型，如果有就排除此日期
                $hasAvoidableEvents = $events->contains(fn($event) => in_array($event->category, $this->avoid_event_types));

                if ($hasAvoidableEvents) {
                    Log::info("Course {$this->id}: {$dateKey} - Has avoidable events, skipping date");
                    return false;
                } else {
                    Log::info("Course {$this->id}: {$dateKey} - No avoidable events, keeping date");
                    return true;
                }
            })
            ->take($this->total_sessions);  // 直到堂數滿足

        // 檢查堂數（邊緣案）
        if ($validDates->count() < $this->total_sessions) {
            Log::warning("Only {$validDates->count()} sessions generated for course {$this->id}, less than requested {$this->total_sessions}");
            // 可擴展: throw new \Exception('無法生成足夠堂數');
        }

        // 準備批量資料（套用時間模板）
        $sessionsData = $validDates->values()->map(function ($date, $index) {
            $sessionStart = $date->copy()->setTimeFrom($this->start_time);  // 套用 start_time 時間
            $sessionEnd = $date->copy()->setTimeFrom($this->end_time ?? $this->start_time->copy()->addHour());
            return [
                'course_id' => $this->id,
                'session_number' => $index + 1,  // 確保從1開始連續編號
                'start_time' => $sessionStart,
                'end_time' => $sessionEnd,
                'status' => 'scheduled',
                'teacher_id' => $this->teacher_id,  // 從課程繼承老師
                'sort_order' => $index + 1,  // 設定排序
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        if (!empty($sessionsData)) {
            CourseSession::insert($sessionsData);  // 批量插入
            Log::info("Batch created {$validDates->count()} weekly sessions starting from {$startDate->format('Y-m-d')} for course " . $this->id);
        }
    }

    /**
     * 生成單次課程的排定
     */
    protected function generateSingleCourseSession(): void
    {
        if (!$this->start_time) {
            Log::warning("Course {$this->id} has no start_time, skipping session generation");
            return;
        }

        if (!$this->end_time) {
            $this->end_time = $this->start_time->copy()->addHours(1);
        }

        CourseSession::create([
            'course_id' => $this->id,
            'session_number' => 1,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => 'scheduled',
            'teacher_id' => $this->teacher_id,  // 從課程繼承老師
            'sort_order' => 1,  // 設定排序
        ]);

        Log::info("Created single session for course " . $this->id);
    }

    /**
     * 設定週期課程的排定條件（用於生成課程排定）
     */
    public function setWeeklyCourseSchedule($startDate, $startTime, $endTime): void
    {
        if ($this->is_weekly_course) {
            // 設定排定條件，不儲存到資料庫
            $this->start_time = Carbon::parse($startDate)->setTimeFrom(Carbon::parse($startTime));
            $this->end_time = Carbon::parse($startDate)->setTimeFrom(Carbon::parse($endTime));
        }
    }

    /**
     * 獲取課程顏色（使用校區顏色）
     */
    protected function getCourseColor(): string
    {
        return $this->campus?->color ?? '#6B7280';
    }

    // 快取清除邏輯移至 ClearsCalendarCache Trait
}
