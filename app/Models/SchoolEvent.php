<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SchoolEvent extends Model implements Eventable
{
    use HasFactory;

    // 可填充欄位
    protected $fillable = [
        'title',           // 事件標題
        'description',     // 事件描述
        'start_time',      // 開始時間
        'end_time',        // 結束時間
        'location',        // 地點
        'category',        // 事件類型
        'status',          // 狀態
        'campus_id',       // 所屬校區
        'created_by',      // 創建者
        'extended_props',  // 擴展屬性
        'sort_order',      // 排序欄位
    ];

    // 日期欄位
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'extended_props' => 'array',
    ];

    // 預設值
    protected $attributes = [
        'location' => '',
        'status' => 'active',
    ];

    // 與用戶的關係
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 與校區的關係
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * 獲取路由鍵名
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * 提供 Guava Calendar 所需的 Schema 方法
     * 解決 SchemaNotFoundException 錯誤
     */
    public function schema(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\Select::make('category')
                    ->label(__('fields.event_type'))
                    ->options([
                        'todo' => __('fields.todo'),
                        'school' => __('fields.school'),
                        'other' => __('fields.other'),
                        'national_holiday' => __('fields.national_holiday'),
                    ])
                    ->disabled(),
                \Filament\Forms\Components\TextInput::make('campus.name')
                    ->label('校區')
                    ->disabled()
                    ->formatStateUsing(fn() => $this->campus?->name ?? ''),
                \Filament\Forms\Components\Textarea::make('description')
                    ->label('事件描述')
                    ->rows(3)
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
                \Filament\Forms\Components\TextInput::make('location')
                    ->label('地點')
                    ->disabled(),
                \Filament\Forms\Components\Select::make('status')
                    ->label('狀態')
                    ->options([
                        'active' => '啟用',
                        'inactive' => '停用',
                    ])
                    ->disabled(),
            ]);
    }


    /**
     * 實現 Eventable 接口
     */
public function toCalendarEvent(): CalendarEvent
    {
        // 根據事件類型生成標題
        $title = $this->getEventTypeLabel($this->category);

        // 為沒有 ID 的事件生成唯一 key
        $key = $this->id ?: 'temp_' . md5($title . $this->start_time->toDateTimeString());

        $event = CalendarEvent::make()
            ->title($title)
            ->start($this->start_time)
            ->end($this->end_time ?? $this->start_time->addHours(2))
            ->allDay(false)
            ->backgroundColor($this->getDefaultEventColor())
            ->textColor('#ffffff')
            ->resourceId('campus_' . $this->campus_id)
            ->key($key)
            ->extendedProps([
                'model' => static::class,
                'type' => 'school_event',
                'model_id' => $this->id,
                'key' => $key,
                'description' => $this->description,
                'campus' => $this->campus?->name,
                'location' => $this->location,
                'category' => $this->category,
                'status' => $this->status,
            ]);

        // 國定假日使用背景顯示
        if ($this->category === 'national_holiday') {
            $event->display('background')
                  ->allDay(true); // 國定假日設為全天事件
        }

        return $event;
    }

    /**
     * 獲取事件類型標籤
     */
    protected function getEventTypeLabel(string $category): string
    {
        return match ($category) {
            'todo' => __('fields.todo'),
            'school' => __('fields.school'),
            'other' => __('fields.other'),
            'national_holiday' => __('fields.national_holiday'),
            default => '未知類型',
        };
    }

    /**
     * 獲取預設事件顏色（使用校區顏色）
     */
    protected function getDefaultEventColor(): string
    {
        // 國定假日使用特殊的紅色背景
        if ($this->category === 'national_holiday') {
            return '#DC2626'; // 紅色背景，表示國定假日
        }

        return $this->campus?->color ?? '#6B7280';
    }

    /**
     * 模型啟動事件
     */
    protected static function boot()
    {
        parent::boot();

        // 當校務事件被更新後，清除行事曆快取
        static::updated(function ($event) {
            $event->clearCalendarCache();
        });

        // 當校務事件被創建後，清除行事曆快取
        static::created(function ($event) {
            $event->clearCalendarCache();
        });

        // 當校務事件被刪除後，清除行事曆快取
        static::deleted(function ($event) {
            $event->clearCalendarCache();
        });
    }

    /**
     * 清除行事曆快取
     */
    public function clearCalendarCache(): void
    {
        try {
            // 檢查是否使用 Redis cache store
            $store = Cache::getStore();

            if ($store instanceof \Illuminate\Cache\RedisStore) {
                // Redis cache - 使用 keys 方法清除所有匹配的快取
                $cacheKeys = Cache::getRedis()->keys('*calendar_events*');
                foreach ($cacheKeys as $key) {
                    // 移除 cache prefix 前綴
                    $cleanKey = str_replace(config('cache.prefix'), '', $key);
                    Cache::forget($cleanKey);
                }
            } else {
                // 非 Redis cache - 使用完整的快取清除策略
                $this->clearAllCalendarCacheKeys();
            }

            Log::info('Calendar cache cleared for school event: ' . $this->id);
        } catch (\Exception $e) {
            Log::error('Failed to clear calendar cache: ' . $e->getMessage());
        }
    }

    /**
     * 清除所有行事曆快取鍵（非 Redis 環境）
     */
    private function clearAllCalendarCacheKeys(): void
    {
        // 清除基本快取鍵
        $baseKeys = [
            'calendar_events_all',
            'calendar_events_active',
            'calendar_events_by_date',
            'calendar_events_by_category',
            'calendar_events_by_campus',
            'calendar_stats'
        ];

        foreach ($baseKeys as $key) {
            Cache::forget($key);
        }

        // 清除日期範圍快取（過去、現在、未來三年）
        $currentYear = now()->year;
        $years = [$currentYear - 1, $currentYear, $currentYear + 1];

        foreach ($years as $year) {
            for ($month = 1; $month <= 12; $month++) {
                // 各種日期格式的快取鍵
                $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
                $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

                // 生成可能的快取鍵模式
                $dateKeys = [
                    "calendar_events_{$year}_{$month}",
                    "calendar_events_{$startOfMonth->format('Y-m-d')}_{$endOfMonth->format('Y-m-d')}",
                ];

                foreach ($dateKeys as $dateKey) {
                    // 各種學校事件和課程的組合
                    $schoolCourseCombinations = [
                        '_school_1_courses_1',
                        '_school_1_courses_0',
                        '_school_0_courses_1',
                        '_school_0_courses_0',
                        ''  // 空字串為基本鍵
                    ];

                    foreach ($schoolCourseCombinations as $combination) {
                        $baseKey = $dateKey . $combination;

                        // 清除各種搜尋、分類、校區的組合
                        $patterns = [
                            $baseKey,
                            $baseKey . '_search_' . md5(''),
                            $baseKey . '_category_',
                            $baseKey . '_campus_',
                            $baseKey . '_sessions',
                            $baseKey . '_search_' . md5('') . '_category_' . '_campus_' . '_sessions',
                        ];

                        foreach ($patterns as $pattern) {
                            Cache::forget($pattern);
                        }
                    }
                }
            }
        }
    }
}
