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
use Filament\Notifications\Notification;

class CalendarWidget extends BaseCalendarWidget
{
    // 預設使用月曆視圖
    protected CalendarViewType $calendarView = CalendarViewType::DayGridMonth;

    // 支援的視圖類型
    protected array $availableViews = [
        CalendarViewType::DayGridMonth,
        CalendarViewType::TimeGridWeek,
        CalendarViewType::TimeGridDay,
    ];

    // 設定週日為第一天
    protected WeekDay $firstDay = WeekDay::Sunday;

    // 設定本地化
    protected ?string $locale = 'zh-tw';

    // 設定每天最大事件數 - 改為 false 避免全部隱藏
    protected bool $dayMaxEvents = false;

    // 使用 Filament 時區
    protected bool $useFilamentTimezone = true;

    // 啟用互動功能
    protected bool $eventClickEnabled = true;
    protected bool $dateClickEnabled = true;
    protected bool $dateSelectEnabled = true;
    protected bool $eventDragEnabled = true;
    protected bool $eventResizeEnabled = true;





    /**
     * 獲取行事曆選項
     * 需要手動配置 headerToolbar 來顯示視圖切換按鈕
     */
    public function getOptions(): array
    {
        return [
            // 顯示事件時間，使用24小時制
            'displayEventTime' => true,
            'displayEventEnd' => true,
            'eventDisplay' => 'block',
            'eventOverlap' => true,
            'selectOverlap' => true,
            'moreLinkClick' => 'popover',
            // 確保跨日事件正確顯示
            'showNonCurrentDates' => true,
            'fixedWeekCount' => false,
            'weekNumbers' => false,
            // 改善事件顯示設定
            'dayMaxEvents' => false,  // 不限制事件數量
            'dayMaxEventRows' => false,  // 不限制事件行數
            'eventLimit' => false,  // 不限制事件
            // 設定24小時制時間格式
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'hour12' => false,
            ],
            'slotLabelFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'hour12' => false,
            ],
            // 確保拖放功能正確配置
            'editable' => true,
            'eventStartEditable' => true,
            'eventDurationEditable' => true,
            'eventResourceEditable' => false,
        ];
    }

    /**
     * 獲取事件內容模板
     */
    public function eventContent(): string
    {
        return '<div class="ec-event-body" x-text="event.title"></div>';
    }

    /**
     * 獲取行事曆事件
     */
    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        return collect()
            ->push(...SchoolEvent::query()
                ->where('status', 'active')
                ->where(function ($query) use ($info) {
                    // 事件開始時間在查詢範圍內
                    $query->whereBetween('start_time', [$info->start, $info->end])
                        // 或事件結束時間在查詢範圍內
                        ->orWhereBetween('end_time', [$info->start, $info->end])
                        // 或事件跨越查詢範圍（開始時間在範圍前，結束時間在範圍後）
                        ->orWhere(function ($q) use ($info) {
                            $q->where('start_time', '<=', $info->start)
                              ->where('end_time', '>=', $info->end);
                        });
                })
                ->with('campus')
                ->get())
            ->push(...Course::query()
                ->where('is_active', true)
                ->where(function ($query) use ($info) {
                    // 事件開始時間在查詢範圍內
                    $query->whereBetween('start_time', [$info->start, $info->end])
                        // 或事件結束時間在查詢範圍內
                        ->orWhereBetween('end_time', [$info->start, $info->end])
                        // 或事件跨越查詢範圍（開始時間在範圍前，結束時間在範圍後）
                        ->orWhere(function ($q) use ($info) {
                            $q->where('start_time', '<=', $info->start)
                              ->where('end_time', '>=', $info->end);
                        });
                })
                ->with('campus')
                ->get());
    }

    /**
     * 獲取行事曆資源（校區）
     * 不使用資源分組，校區用顏色區分
     */
    protected function getResources(): Collection|array|Builder
    {
        return collect();
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

        // 更新資料庫中的事件時間
        $event->start_time = $newStart;
        $event->end_time = $newEnd;
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
        return $this->createAction(SchoolEvent::class);
    }

    /**
     * 創建課程動作
     */
    public function createCourseAction(): CreateAction
    {
        return $this->createAction(Course::class);
    }
}
