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

    /**
     * 重設篩選器
     */
    public function resetFilters(): void
    {
        $this->filterData = [
            'search' => '',
            'showSchoolEvents' => true,
            'showCourses' => true,
            'category' => '',
            'campus_id' => '',
        ];
        $this->showSchoolEvents = true;
        $this->showCourses = true;
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
     * 設定行事曆選項 - 限制每天最多顯示3筆事件並設定24小時制
     */
    public function getOptions(): array
    {
        $options = parent::getOptions();

        // 設定每天最大事件數為3筆，超過顯示MORE
        $options['dayMaxEvents'] = 3;

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
            // 搜尋欄位
            Action::make('search')
                ->label('搜尋')
                ->icon('heroicon-o-magnifying-glass')
                ->color('gray')
                ->form([
                    TextInput::make('search')
                        ->label('搜尋')
                        ->placeholder('搜尋...')
                        ->default($this->filterData['search'])
                        ->live()
                        ->debounce(500),
                ])
                ->fillForm([
                    'search' => $this->filterData['search'],
                ])
                ->action(function (array $data) {
                    $this->filterData['search'] = $data['search'];
                    $this->dispatch('calendar--refresh');
                }),

            // 篩選器下拉選單
            Action::make('filters')
                ->label('篩選')
                ->icon('heroicon-o-funnel')
                ->color('gray')
                ->form([
                    Toggle::make('showSchoolEvents')
                        ->label('顯示校務事件')
                        ->default($this->showSchoolEvents),
                    Toggle::make('showCourses')
                        ->label('顯示課程')
                        ->default($this->showCourses),
                    Select::make('category')
                        ->label('事件類型')
                        ->options([
                            '' => '全部',
                            'national_holiday' => '國定假日',
                            'periodic_assessment' => '定期評量',
                            'disaster_drill' => '防災演練',
                            'school_anniversary' => '校慶活動',
                            'todo' => '代辦事項',
                            'other' => '其他',
                        ])
                        ->default($this->filterData['category']),
                    Select::make('campus_id')
                        ->label('校區')
                        ->options(Campus::pluck('name', 'id'))
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
                    $this->dispatch('calendar--refresh');
                }),

            // 視圖切換下拉選單
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
        $cacheKey = 'calendar_events_' . $info->start->format('Y-m-d') . '_' . $info->end->format('Y-m-d') .
                   '_school_' . ($this->showSchoolEvents ? '1' : '0') .
                   '_courses_' . ($this->showCourses ? '1' : '0') .
                   '_search_' . md5($this->filterData['search']) .
                   '_category_' . $this->filterData['category'] .
                   '_campus_' . $this->filterData['campus_id'];

        return Cache::remember($cacheKey, 60, function () use ($info) {
            // 調試：記錄過濾狀態
            Log::info('Calendar filter states:', [
                'showSchoolEvents' => $this->showSchoolEvents,
                'showCourses' => $this->showCourses,
                'filterData' => $this->filterData,
            ]);

            $events = collect();

            // 查詢校務事件（根據過濾設定）
            if ($this->showSchoolEvents) {
                $schoolEventsQuery = SchoolEvent::query()
                    ->where('status', 'active')
                    ->where(function ($query) use ($info) {
                        $query->where('start_time', '<=', $info->end)
                              ->where('end_time', '>=', $info->start);
                    })
                    ->with('campus');

                // 應用搜尋篩選
                if (!empty($this->filterData['search'])) {
                    $search = $this->filterData['search'];
                    $schoolEventsQuery->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%");
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
                $coursesQuery = Course::query()
                    ->where('is_active', true)
                    ->where(function ($query) use ($info) {
                        $query->where('start_time', '<=', $info->end)
                              ->where('end_time', '>=', $info->start);
                    })
                    ->with('campus');

                // 應用搜尋篩選
                if (!empty($this->filterData['search'])) {
                    $search = $this->filterData['search'];
                    $coursesQuery->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%");
                    });
                }

                // 應用校區篩選
                if (!empty($this->filterData['campus_id'])) {
                    $coursesQuery->where('campus_id', $this->filterData['campus_id']);
                }

                $courses = $coursesQuery->get();
                $events = $events->merge($courses);
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

        Notification::make()
            ->title('事件調整')
            ->body("事件已調整結束時間到 {$newEnd->format('Y-m-d H:i')}")
            ->success()
            ->send();

        return true;
    }

    /**
     * 獲取事件類型標籤
     */
    protected function getEventTypeLabel(string $type): string
    {
        return match ($type) {
            'school_events' => '校務事件',
            'courses' => '課程',
            'school_event' => '校務事件',
            'course' => '課程',
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
     * 覆寫預設動作以使用中文標籤
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
