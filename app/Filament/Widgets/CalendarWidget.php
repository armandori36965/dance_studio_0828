<?php

namespace App\Filament\Widgets;

use App\Models\SchoolEvent;
use App\Models\Course;
use App\Models\Campus;
use Guava\Calendar\Filament\CalendarWidget as BaseCalendarWidget;
use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\Enums\Context;
use Guava\Calendar\ValueObjects\FetchInfo;
use Guava\Calendar\ValueObjects\EventClickInfo;
use Guava\Calendar\ValueObjects\DateClickInfo;
use Guava\Calendar\ValueObjects\DateSelectInfo;
use Guava\Calendar\Filament\Actions\CreateAction;
use Carbon\WeekDay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class CalendarWidget extends BaseCalendarWidget implements HasActions
{
    use InteractsWithActions, InteractsWithForms;

    // 事件過濾狀態
    public bool $showSchoolEvents = true;
    public bool $showCourses = true;

    // 篩選器表單數據
    public array $filterData = [
        'search' => '',
        'showSchoolEvents' => true,
        'showCourses' => true,
        'category' => '',
        'campus_id' => '',
    ];

    // 即時搜尋屬性
    public string $tableSearch = '';

    /**
     * 處理即時搜尋更新
     */
    public function updatedTableSearch(): void
    {
        $this->filterData['search'] = $this->tableSearch;
        Log::info('Calendar table search updated:', ['search' => $this->tableSearch]);
        $this->dispatch('calendar--refresh');
    }

    // 預設使用月視圖
    protected CalendarViewType $calendarView = CalendarViewType::DayGridMonth;

    // 設定週日為第一天
    protected WeekDay $firstDay = WeekDay::Sunday;

    // 設定本地化
    protected ?string $locale = 'zh-tw';

    // 設定每天最大事件數 - 啟用限制功能
    protected bool $dayMaxEvents = true;

    // 使用 Filament 時區
    protected bool $useFilamentTimezone = true;

    // 啟用互動功能
    protected bool $eventClickEnabled = true;
    protected bool $dateClickEnabled = true;
    protected bool $dateSelectEnabled = true;
    protected bool $eventDragEnabled = true;
    protected bool $eventResizeEnabled = true;

    // 臨時禁用上下文菜單以修復渲染問題（註解）
    // protected bool $datesSetEnabled = false;
    // protected bool $viewDidMountEnabled = false;

    // 預設事件點擊動作
    protected ?string $defaultEventClickAction = 'view';

    /**
     * 調試：檢查 JavaScript 資源載入
     */
    public function getEventsJs(array $info): array
    {
        Log::info('getEventsJs called with info:', $info);

        // 調用父類方法
        $result = parent::getEventsJs($info);

        Log::info('getEventsJs returning result:', [
            'count' => count($result),
            'result' => $result
        ]);

        return $result;
    }

    /**
     * 調試：檢查行事曆視圖配置
     */
    public function getCalendarView(): CalendarViewType
    {
        $view = parent::getCalendarView();
        Log::info('Calendar view:', ['view' => $view->value]);
        return $view;
    }

    /**
     * 設定行事曆選項 - 固定日期格子大小並設定24小時制
     */
    public function getOptions(): array
    {
        $options = parent::getOptions();

        // 設定每天最大事件數（數字值，FullCalendar 支援）
        $options['dayMaxEvents'] = 3;

        // 設定固定週模式，確保日期格子大小一致
        $options['fixedWeekCount'] = true; // 固定顯示6週
        $options['showNonCurrentDates'] = true; // 顯示非當月日期

        // 設定24小時制時間格式
        $options['eventTimeFormat'] = [
            'hour' => '2-digit',
            'minute' => '2-digit',
            'hour12' => false, // 使用24小時制
        ];

        // 設定時間軸標籤格式為24小時制
        $options['slotLabelFormat'] = [
            'hour' => '2-digit',
            'minute' => '2-digit',
            'hour12' => false, // 使用24小時制
        ];

        Log::info('Calendar options:', ['options' => $options]);
        return $options;
    }

    /**
     * 設定行事曆標題
     */
    public function getHeading(): string|\Illuminate\Support\HtmlString|null
    {
        return '行事曆';
    }





    // 使用預設配置
    protected array $options = [];

    /**
     * 獲取 header actions - 視圖切換按鈕和過濾按鈕
     */
    public function getHeaderActions(): array
    {
        return [
            // 即時搜尋欄位
            Action::make('search')
                ->label('搜尋')
                ->icon('heroicon-o-magnifying-glass')
                ->color('gray')
                ->form([
                    TextInput::make('search')
                        ->label('搜尋事件')
                        ->placeholder('搜尋事件標題或描述...')
                        ->default($this->filterData['search'])
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->filterData['search'] = $state;
                            Log::info('Calendar search updated:', ['search' => $state]);
                            $this->dispatch('calendar--refresh');
                        }),
                ])
                ->fillForm([
                    'search' => $this->filterData['search'],
                ])
                ->action(function (array $data) {
                    $this->filterData['search'] = $data['search'];
                    Log::info('Calendar search applied:', $data);
                    $this->dispatch('calendar--refresh');
                }),

            // 篩選器下拉選單
            Action::make('filters')
                ->label('篩選')
                ->icon('heroicon-o-funnel')
                ->color('gray')
                ->form([
                    Toggle::make('showSchoolEvents')
                        ->label('顯示事件')
                        ->default($this->showSchoolEvents),
                    Toggle::make('showCourses')
                        ->label('顯示課程')
                        ->default($this->showCourses),
                    Select::make('category')
                        ->label('事件類型')
                        ->options(function () {
                            // 動態獲取資料庫中實際存在的事件類型
                            $categories = \App\Models\SchoolEvent::select('category')
                                ->distinct()
                                ->whereNotNull('category')
                                ->pluck('category')
                                ->filter()
                                ->mapWithKeys(function ($category) {
                                    return [$category => match ($category) {
                                        'todo' => '代辦事項',
                                        'school' => '校務',
                                        'other' => '其他',
                                        'national_holiday' => '國定假日',
                                        default => $category,
                                    }];
                                })
                                ->sort();

                            return ['' => '全部'] + $categories->toArray();
                        })
                        ->default($this->filterData['category']),
                    Select::make('campus_id')
                        ->label('校區')
                        ->options(function () {
                            // 動態獲取校區，按排序欄位排序
                            return ['' => '全部校區'] + \App\Models\Campus::orderBy('sort_order', 'asc')
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->default($this->filterData['campus_id']),
                ])
                ->fillForm([
                    'showSchoolEvents' => $this->showSchoolEvents,
                    'showCourses' => $this->showCourses,
                    'category' => $this->filterData['category'],
                    'campus_id' => $this->filterData['campus_id'],
                ])
                ->action(function (array $data) {
                    $this->showSchoolEvents = $data['showSchoolEvents'];
                    $this->showCourses = $data['showCourses'];
                    $this->filterData['category'] = $data['category'];
                    $this->filterData['campus_id'] = $data['campus_id'];

                    Log::info('Calendar filters applied:', $data);
                    $this->dispatch('calendar--refresh');
                }),

            // 視圖切換下拉選單（替代欄位管理）
            Action::make('view_switcher')
                ->label('視圖')
                ->icon('heroicon-o-squares-2x2')
                ->color('gray')
                ->form([
                    Select::make('view')
                        ->label('選擇視圖')
                        ->options([
                            'dayGridMonth' => '月視圖',
                            'timeGridWeek' => '週視圖',
                            'timeGridDay' => '日視圖',
                            'listWeek' => '列表視圖',
                        ])
                        ->default($this->getCurrentView()),
                ])
                ->fillForm([
                    'view' => $this->getCurrentView(),
                ])
                ->action(function (array $data) {
                    $this->setOption('view', $data['view']);
                    Log::info('Calendar view changed:', $data);
                }),
        ];
    }

    /**
     * 獲取當前視圖
     */
    protected function getCurrentView(): string
    {
        return $this->options['view'] ?? 'dayGridMonth';
    }

    /**
     * 獲取行事曆事件 - 查詢校務事件和課程
     */
    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        // 調試：記錄 FetchInfo 信息
        Log::info('Calendar FetchInfo:', [
            'start' => $info->start->toDateTimeString(),
            'end' => $info->end->toDateTimeString(),
        ]);

        // 建立快取鍵，包含日期範圍和過濾狀態以確保數據準確性
        $searchTerm = $this->tableSearch ?: $this->filterData['search'];
        $cacheKey = 'calendar_events_v2_' . $info->start->format('Y-m-d') . '_' . $info->end->format('Y-m-d') .
                   '_school_' . ($this->showSchoolEvents ? '1' : '0') .
                   '_courses_' . ($this->showCourses ? '1' : '0') .
                   '_search_' . md5($searchTerm) .
                   '_category_' . $this->filterData['category'] .
                   '_campus_' . $this->filterData['campus_id'] .
                   '_holidays_always'; // 國定假日始終顯示標識

        return Cache::remember($cacheKey, 10, function () use ($info) {
            // 調試：記錄過濾狀態
            Log::info('Calendar filter states:', [
                'showSchoolEvents' => $this->showSchoolEvents,
                'showCourses' => $this->showCourses,
                'filterData' => $this->filterData,
            ]);

            $events = collect();

            // 國定假日始終顯示，不受「顯示事件」開關影響
            $nationalHolidaysQuery = SchoolEvent::query()
                ->where('status', 'active')
                ->where('category', 'national_holiday')
                ->where(function ($query) use ($info) {
                    $query->where('start_time', '<=', $info->end)
                          ->where('end_time', '>=', $info->start);
                })
                ->with('campus');

            // 對國定假日也應用搜尋篩選（如果有）
            $searchTerm = $this->tableSearch ?: $this->filterData['search'];
            if (!empty($searchTerm)) {
                $nationalHolidaysQuery->where(function ($query) use ($searchTerm) {
                    $query->where('description', 'like', "%{$searchTerm}%")
                          ->orWhere('location', 'like', "%{$searchTerm}%");
                });
            }

            // 國定假日不受校區篩選影響（因為是全國性的）
            $nationalHolidays = $nationalHolidaysQuery->get();
            $events = $events->merge($nationalHolidays);

            // 查詢其他校務事件（根據過濾設定）
            if ($this->showSchoolEvents) {
                $schoolEventsQuery = SchoolEvent::query()
                    ->where('status', 'active')
                    ->where('category', '!=', 'national_holiday') // 排除國定假日，因為已經單獨處理
                    ->where(function ($query) use ($info) {
                        $query->where('start_time', '<=', $info->end)
                              ->where('end_time', '>=', $info->start);
                    })
                    ->with('campus');

                // 應用搜尋篩選
                if (!empty($searchTerm)) {
                    $schoolEventsQuery->where(function ($query) use ($searchTerm) {
                        $query->where('description', 'like', "%{$searchTerm}%")
                              ->orWhere('location', 'like', "%{$searchTerm}%");
                    });
                }

                // 應用類型篩選
                if (!empty($this->filterData['category'])) {
                    $schoolEventsQuery->where('category', $this->filterData['category']);
                }

                // 應用校區篩選
                if (!empty($this->filterData['campus_id'])) {
                    $schoolEventsQuery->where('campus_id', $this->filterData['campus_id']);
                }

                $schoolEvents = $schoolEventsQuery->get();
                $events = $events->merge($schoolEvents);
            }

            // 查詢課程事件（根據過濾設定）
            if ($this->showCourses) {
                // 1. 查詢課程排定日期（CourseSession）
                $courseSessionsQuery = \App\Models\CourseSession::query()
                    ->where('start_time', '<=', $info->end)
                    ->where('end_time', '>=', $info->start)
                    ->with(['course.campus']);

                // 應用搜尋篩選
                if (!empty($this->filterData['search'])) {
                    $search = $this->filterData['search'];
                    $courseSessionsQuery->whereHas('course', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%");
                    });
                }

                // 應用校區篩選
                if (!empty($this->filterData['campus_id'])) {
                    $courseSessionsQuery->whereHas('course', function ($query) {
                        $query->where('campus_id', $this->filterData['campus_id']);
                    });
                }

                $courseSessions = $courseSessionsQuery->get();
                $events = $events->merge($courseSessions);

                // 移除 Course 查詢，避免與 CourseSession 重複
                // 現在只使用 CourseSession 來顯示課程事件
            }

            // 如果沒有事件且校務事件被顯示，添加測試事件
            if ($events->isEmpty() && $this->showSchoolEvents) {
                $testEvent = new SchoolEvent([
                    'id' => 999, // 給測試事件一個臨時 ID
                    'title' => '測試事件',
                    'start_time' => now()->startOfDay()->addHours(9),
                    'end_time' => now()->startOfDay()->addHours(11),
                    'status' => 'active',
                    'campus_id' => 1,
                    'description' => '這是一個測試事件',
                    'category' => 'test',
                ]);
                // 確保測試事件有 campus 關聯
                $testEvent->setRelation('campus', Campus::find(1));
                $events->push($testEvent);
            }

            // 調試：記錄事件數量
            Log::info('Calendar events count: ' . $events->count());
            if ($events->isNotEmpty()) {
                Log::info('First event: ' . $events->first()->title);
            }

            return $events;
        });
    }

    /**
     * 獲取行事曆資源（校區）
     */
    protected function getResources(): Collection|array|Builder
    {
        return Campus::query()
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * 事件點擊處理
     */
    protected function onEventClick(EventClickInfo $info, Model $event, ?string $action = null): void
    {
        // 如果沒有指定動作，顯示通知
        if (!$action) {
            $calendarEvent = $info->event;
            $title = $calendarEvent->title ?? '無標題';

            Notification::make()
                ->title('事件名稱')
                ->body($title)
                ->info()
                ->send();
            return;
        }

        // 如果有指定動作，觸發該動作
        $this->mountAction($action);
    }



    /**
     * 事件拖放處理
     */
    protected function onEventDrop($info, $event): bool
    {
        $calendarEvent = $info->event;
        $newStart = $calendarEvent->getStart();
        $newEnd = $calendarEvent->getEnd();

        // 計算時間差並更新事件
        $duration = $event->end_time->diffInMinutes($event->start_time);
        $event->start_time = $newStart;
        $event->end_time = $newStart->copy()->addMinutes($duration);
        $event->save();

        // 清除相關快取，確保資料更新後立即反映
        $this->clearCalendarCache();

        Notification::make()
            ->title('事件移動')
            ->body("事件已移動到 {$newStart->format('Y-m-d H:i')}")
            ->success()
            ->send();

        return true;
    }

    /**
     * 事件調整大小處理
     */
    public function onEventResize($info, $event): bool
    {
        $calendarEvent = $info->event;
        $newEnd = $calendarEvent->getEnd();

        // 更新資料庫中的事件結束時間
        $event->end_time = $newEnd;
        $event->save();

        // 清除相關快取，確保資料更新後立即反映
        $this->clearCalendarCache();

        Notification::make()
            ->title('事件調整')
            ->body("事件已調整結束時間到 {$newEnd->format('Y-m-d H:i')}")
            ->success()
            ->send();

        return true;
    }

    /**
     * 清除行事曆快取
     */
    protected function clearCalendarCache(): void
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
                // 非 Redis cache - 清除已知的快取鍵模式
                $this->clearAllCalendarCacheKeys();
            }

            // 觸發行事曆重新整理
            $this->dispatch('calendar--refresh');

            Log::info('Calendar cache cleared and refresh triggered');
        } catch (\Exception $e) {
            Log::error('Failed to clear calendar cache: ' . $e->getMessage());
            // 即使清除失敗也要重新整理
            $this->dispatch('calendar--refresh');
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

        // 清除日期範圍快取
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;
        $prevYear = $currentYear - 1;

        foreach ([$prevYear, $currentYear, $nextYear] as $year) {
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
                    // 各種組合的快取鍵
                    $combinations = [
                        // 基本組合
                        $dateKey,
                        $dateKey . '_school_1_courses_1',
                        $dateKey . '_school_1_courses_0',
                        $dateKey . '_school_0_courses_1',
                        $dateKey . '_school_0_courses_0',
                    ];

                    foreach ($combinations as $key) {
                        // 各種搜尋和篩選的組合
                        Cache::forget($key);
                        Cache::forget($key . '_search_' . md5(''));
                        Cache::forget($key . '_category_');
                        Cache::forget($key . '_campus_');
                        Cache::forget($key . '_sessions');
                        Cache::forget($key . '_search_' . md5('') . '_category_' . '_campus_' . '_sessions');
                    }
                }
            }
        }
    }

    /**
     * 獲取事件類型標籤
     */
    protected function getEventTypeLabel(string $type): string
    {
        return match ($type) {
            'school_events' => '事件',
            'courses' => '課程',
            'school_event' => '事件',
            'course' => '課程',
            'course_sessions' => '課程排定',
            'course_session' => '課程排定',
            default => '未知類型',
        };
    }

    /**
     * 獲取事件記錄的路由鍵名
     */
    protected function getEventRecordRouteKeyName(?string $model = null): ?string
    {
        return 'id';
    }

    /**
     * 提供 Guava Calendar 所需的 Schema 方法
     * 根據當前事件記錄類型調用對應模型的 schema 方法
     */
    public function schema(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        $record = $this->getEventRecord();

        if (!$record) {
            return $schema->components([]);
        }

        // 確保載入必要的關聯資料
        if ($record instanceof \App\Models\CourseSession) {
            $record->load(['course.campus', 'teacher']);
        } elseif ($record instanceof \App\Models\SchoolEvent) {
            $record->load(['campus']);
        }

        // 如果記錄有 schema 方法，直接調用
        if (method_exists($record, 'schema')) {
            return $record->schema($schema);
        }

        // 如果沒有，返回空 schema
        return $schema->components([]);
    }

    /**
     * 覆寫預設動作以使用中文標籤並處理不同模型類型
     */
    public function viewAction(): \Guava\Calendar\Filament\Actions\ViewAction
    {
        return parent::viewAction()
            ->label('檢視')
            ->modalHeading(fn () => '檢視 ' . $this->getEventTypeLabel($this->getEventRecord()?->getTable() ?? ''));
    }

    public function editAction(): \Guava\Calendar\Filament\Actions\EditAction
    {
        return parent::editAction()
            ->label('編輯')
            ->modalHeading(fn () => '編輯 ' . $this->getEventTypeLabel($this->getEventRecord()?->getTable() ?? ''));
    }

    public function deleteAction(): \Guava\Calendar\Filament\Actions\DeleteAction
    {
        return parent::deleteAction()
            ->label('刪除')
            ->modalHeading(fn () => '刪除 ' . $this->getEventTypeLabel($this->getEventRecord()?->getTable() ?? ''));
    }

    /**
     * 創建校務活動動作
     */
    public function createSchoolEventAction(): CreateAction
    {
        return $this->createAction(SchoolEvent::class)
            ->label('新增校務事件')
            ->modalHeading('新增校務事件');
    }

    /**
     * 創建課程動作
     */
    public function createCourseAction(): CreateAction
    {
        return $this->createAction(Course::class)
            ->label('新增課程')
            ->modalHeading('新增課程');
    }

    /**
     * 日期點擊上下文選單動作
     */
    protected function getDateClickContextMenuActions(): array
    {
        return [
            $this->createSchoolEventAction(),
            $this->createCourseAction(),
        ];
    }

    /**
     * 事件點擊上下文選單動作
     */
    protected function getEventClickContextMenuActions(): array
    {
        return [
            $this->viewAction(),
            $this->editAction(),
            $this->deleteAction(),
        ];
    }
}
