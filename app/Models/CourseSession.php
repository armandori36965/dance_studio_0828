<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use App\Traits\ClearsCalendarCache;

class CourseSession extends Model implements Eventable
{
    use ClearsCalendarCache;

    protected $fillable = [
        'course_id',
        'session_number',
        'start_time',
        'end_time',
        'status',
        'notes',
        'sort_order',
        'teacher_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // 與課程的關係
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // 與授課老師的關係
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * 模型啟動事件
     */
    protected static function boot()
    {
        parent::boot();

        // 當課程排定被創建後，清除行事曆快取
        static::created(function ($session) {
            $session->clearCalendarCache();
        });

        // 當課程排定被更新後，清除行事曆快取
        static::updated(function ($session) {
            $session->clearCalendarCache();
        });

        // 當課程排定被刪除後，清除行事曆快取
        static::deleted(function ($session) {
            $session->clearCalendarCache();
        });
    }

    /**
     * 提供 Guava Calendar 所需的 Schema 方法
     * 解決 SchemaNotFoundException 錯誤
     */
    public function schema(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\TextInput::make('course_name')
                    ->label('課程名稱')
                    ->disabled()
                    ->formatStateUsing(fn() => $this->course?->name ?? ''),
                \Filament\Forms\Components\TextInput::make('campus_name')
                    ->label('學校')
                    ->disabled()
                    ->formatStateUsing(fn() => $this->course?->campus?->name ?? ''),
                \Filament\Forms\Components\TextInput::make('teacher_name')
                    ->label('授課老師')
                    ->disabled()
                    ->formatStateUsing(fn() => $this->teacher?->name ?? '未指定'),
                \Filament\Forms\Components\TextInput::make('session_number')
                    ->label('堂數')
                    ->numeric()
                    ->disabled(),
                \Filament\Forms\Components\DateTimePicker::make('start_time')
                    ->label('開始時間')
                    ->disabled()
                    ->displayFormat('Y-m-d H:i')
                    ->native(false), // 強制不使用原生時間選擇器
                \Filament\Forms\Components\DateTimePicker::make('end_time')
                    ->label('結束時間')
                    ->disabled()
                    ->displayFormat('Y-m-d H:i')
                    ->native(false), // 強制不使用原生時間選擇器
                \Filament\Forms\Components\Select::make('status')
                    ->label('狀態')
                    ->options([
                        'scheduled' => '已排定',
                        'completed' => '已完成',
                        'cancelled' => '已取消',
                    ])
                    ->disabled(),
                \Filament\Forms\Components\Textarea::make('notes')
                    ->label('備註')
                    ->rows(3)
                    ->disabled(),
            ]);
    }

    /**
     * 實現 Eventable 接口
     */
    public function toCalendarEvent(): CalendarEvent
    {
        // 確保課程關聯已載入
        if (!$this->relationLoaded('course')) {
            $this->load('course.campus');
        }

        $key = $this->id ?: 'temp_' . md5($this->course_id . $this->session_number . $this->start_time->toDateTimeString());

        return CalendarEvent::make()
            ->title(($this->course?->name ?? '未知課程') . ' (第' . $this->session_number . '堂)')
            ->start($this->start_time)
            ->end($this->end_time)
            ->allDay(false)
            ->backgroundColor($this->getSessionColor())
            ->textColor('#ffffff')
            ->resourceId('campus_' . ($this->course?->campus_id ?? 0))
            ->key($key)
            ->extendedProps([
                'model' => static::class,
                'type' => 'course_session',
                'model_id' => $this->id,
                'course_id' => $this->course_id,
                'session_number' => $this->session_number,
                'key' => $key,
                'description' => $this->course?->description ?? '',
                'campus' => $this->course?->campus?->name ?? '',
                'level' => $this->course?->level ?? '',
                'price' => $this->course?->price ?? 0,
                'student_count' => $this->course?->student_count ?? 0,
                'status' => $this->status,
            ]);
    }

    /**
     * 獲取課程排定日期顏色
     */
    protected function getSessionColor(): string
    {
        return match ($this->status) {
            'completed' => '#10B981', // 綠色 - 已完成
            'cancelled' => '#EF4444', // 紅色 - 已取消
            'scheduled' => $this->course?->campus?->color ?? '#6B7280', // 校區顏色 - 已排定
            default => '#6B7280',
        };
    }

    // 快取清除邏輯移至 ClearsCalendarCache Trait
}
