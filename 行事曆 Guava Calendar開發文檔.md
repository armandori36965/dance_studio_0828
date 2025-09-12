# 舞蹈工作室管理系統 - 行事曆開發文檔

## 📋 文檔概述

本文檔詳細說明如何使用 Guava Calendar 插件在 Filament 4.x 中開發行事曆功能，遵循專案開發規範。

**⚠️ 重要提醒：開發前必須完整閱讀此文檔！**

---

## 🎯 技術架構

### 核心組件

-   **Laravel 12.x** - 後端框架
-   **Filament 4.x** - 管理面板
-   **Guava Calendar 2.x** - 行事曆插件
-   **SQLite** - 資料庫

### 行事曆功能範圍

-   ✅ 校務活動管理
-   ✅ 課程排程管理
-   ✅ 多校區資源管理
-   ✅ 事件拖放調整
-   ✅ 多視圖切換
-   ✅ 事件互動操作

---

## 🛠️ 安裝與配置

### 1. 套件安裝

```bash
# 安裝 Guava Calendar 套件
composer require guava/calendar

# 發布套件資源
php artisan filament:assets
```

### 2. 主題配置

在 `resources/css/filament/theme.css` 中添加：

```css
/* 引入 Guava Calendar 樣式 */
@source '../../../../vendor/guava/calendar/resources/**/*';

/* 可選：應用主題樣式 */
@import "../../../../vendor/guava/calendar/resources/css/theme.css";
```

### 3. 資料庫遷移

確保以下遷移檔案已執行：

```bash
php artisan migrate
```

### 4. 建立自訂 Widget

使用 artisan 命令建立行事曆 Widget：

```bash
php artisan make:filament-widget
```

**⚠️ 重要：建立後必須移除 `view` 屬性！**

---

## 📁 專案結構

### 行事曆相關檔案結構

```
dance_studio_0828/
├── app/
│   ├── Filament/
│   │   └── Widgets/
│   │       ├── CalendarWidget.php          # 主行事曆 Widget
│   │       └── CalendarStatsWidget.php     # 行事曆統計 Widget
│   └── /Models
│       ├── SchoolEvent.php                 # 校務活動模型
│       ├── Course.php                      # 課程模型
│       └── Campus.php                      # 校區模型
├── database/
│   └── seeders/
│       └── CalendarSampleDataSeeder.php    # 行事曆範例資料
└── app/Filament/Resources/
    ├── SchoolEvents/                       # 校務活動管理資源
    ├── Courses/                            # 課程管理資源
    └── Campuses/                           # 校區管理資源
```

---

## 🔧 核心組件開發

### 1. 行事曆 Widget 重構原則

#### 重構目標

**⚠️ 重要：遵循 Filament 4.x 和 SDUI 原則**

-   **檔案大小控制**：CalendarWidget.php 應控制在 200-300 行
-   **單一職責原則**：Widget 只負責核心行事曆功能和互動處理
-   **模組化設計**：使用服務類和 Trait 分離功能
-   **SDUI 原則**：使用 PHP 結構化配置對象定義 UI，避免複雜前端邏輯

#### 基本 CalendarWidget 結構（符合官方規範）

```php
<?php

namespace App\Filament\Widgets;

use Guava\Calendar\Widgets\CalendarWidget as BaseCalendarWidget;
use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\ValueObjects\FetchInfo;
use Guava\Calendar\ValueObjects\EventClickInfo;
use Guava\Calendar\ValueObjects\EventDropInfo;
use Guava\Calendar\ValueObjects\EventResizeInfo;
use Guava\Calendar\Filament\Actions\CreateAction;
use Carbon\WeekDay;
use App\Models\SchoolEvent;
use App\Models\Course;
use App\Models\Campus;
use Illuminate\Database\Eloquent\Model;

class CalendarWidget extends BaseCalendarWidget
{
    // 預設使用資源時間網格週視圖
    protected CalendarViewType $calendarView = CalendarViewType::ResourceTimeGridWeek;

    // 設定週日為第一天（Guava Calendar 方式）
    protected WeekDay $firstDay = WeekDay::Sunday;

    // 設定本地化（Guava Calendar 方式）
    protected ?string $locale = 'zh-tw';

    // 設定每天最大事件數（Guava Calendar 方式）
    protected bool $dayMaxEvents = true;

    // 使用 Filament 時區（Guava Calendar 方式）
    protected bool $useFilamentTimezone = true;

    // 啟用互動功能
    protected bool $eventClickEnabled = true;
    protected bool $dateClickEnabled = true;
    protected bool $dateSelectEnabled = true;
    protected bool $eventDragEnabled = true;
    protected bool $eventResizeEnabled = true;

    /**
     * 獲取行事曆選項（Guava Calendar 方式）
     */
    public function getOptions(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek,resourceTimeGridWeek'
            ],
            'buttonText' => [
                'today' => '今天',
                'month' => '月',
                'week' => '週',
                'day' => '日',
                'list' => '列表',
                'resourceTimeGridWeek' => '資源週'
            ],
            'resourceAreaWidth' => '20%',
            'resourceLabelText' => '校區',
        ];
    }

    /**
     * 獲取行事曆事件
     */
    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        return collect()
            ->push(...SchoolEvent::query()
                ->where('status', 'active')
                ->whereDate('start_time', '>=', $info->start)
                ->whereDate('start_time', '<=', $info->end)
                ->with('campus')
                ->get())
            ->push(...Course::query()
                ->where('is_active', true)
                ->whereDate('start_time', '>=', $info->start)
                ->whereDate('start_time', '<=', $info->end)
                ->with('campus')
                ->get());
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
        $calendarEvent = $info->event;
        $extendedProps = $calendarEvent->extendedProps ?? [];

        $title = $calendarEvent->title ?? '無標題';
        $type = $this->getEventTypeLabel($extendedProps['type'] ?? '');
        $campus = $extendedProps['campus'] ?? '未知校區';

        Notification::make()
            ->title('事件詳情')
            ->body("標題：{$title}\n類型：{$type}\n校區：{$campus}")
            ->info()
            ->send();
    }

    /**
     * 事件拖放處理
     */
    protected function onEventDrop(EventDropInfo $info, Model $event): bool
    {
        $calendarEvent = $info->event;
        $newStart = $calendarEvent->getStart()->format('Y-m-d H:i');

        // 更新資料庫中的事件時間
        $this->updateEventTime($calendarEvent, $newStart, null);

        Notification::make()
            ->title('事件移動')
            ->body("事件已移動到 {$newStart}")
            ->success()
            ->send();

        return true;
    }

    /**
     * 事件調整大小處理
     */
    public function onEventResize(EventResizeInfo $info, Model $event): bool
    {
        $calendarEvent = $info->event;
        $newEnd = $calendarEvent->getEnd()->format('Y-m-d H:i');

        // 更新資料庫中的事件結束時間
        $this->updateEventTime($calendarEvent, null, $newEnd);

        Notification::make()
            ->title('事件調整')
            ->body("事件已調整結束時間到 {$newEnd}")
            ->success()
            ->send();

        return true;
    }

    /**
     * 更新事件時間的輔助方法
     */
    protected function updateEventTime($calendarEvent, ?string $newStart, ?string $newEnd): void
    {
        $extendedProps = $calendarEvent->extendedProps ?? [];
        $type = $extendedProps['type'] ?? '';
        $modelId = $extendedProps['model_id'] ?? null;

        if (!$modelId) return;

        try {
            switch ($type) {
                case 'school_event':
                    $event = SchoolEvent::find($modelId);
                    if ($event) {
                        if ($newStart) $event->start_time = $newStart;
                        if ($newEnd) $event->end_time = $newEnd;
                        $event->save();
                    }
                    break;
                case 'course':
                    $course = Course::find($modelId);
                    if ($course) {
                        if ($newStart) $course->start_time = $newStart;
                        if ($newEnd) $course->end_time = $newEnd;
                        $course->save();
                    }
                    break;
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('更新失敗')
                ->body('無法更新事件時間：' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * 獲取事件類型標籤
     */
    protected function getEventTypeLabel(string $type): string
    {
        return match ($type) {
            'school_event' => '校務活動',
            'course' => '課程',
            default => '未知類型',
        };
    }

    /**
     * 創建校務活動動作（符合官方規範）
     */
    public function createSchoolEventAction(): CreateAction
    {
        return $this->createAction(SchoolEvent::class);
    }

    /**
     * 創建課程動作（符合官方規範）
     */
    public function createCourseAction(): CreateAction
    {
        return $this->createAction(Course::class);
    }
}
```

### 2. 官方規範功能補充

#### 上下文選單功能

根據官方文檔，可以實現上下文選單來提供更多互動選項：

```php
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
```

#### 自訂義事件內容

```php
/**
 * 自訂義事件內容
 */
protected function eventContent(): \Illuminate\Support\HtmlString|string
{
    return view('calendar.event');
}
```

#### 自訂義資源標籤內容

```php
/**
 * 自訂義資源標籤內容
 */
protected function resourceLabelContent(): \Illuminate\Support\HtmlString|string
{
    return view('calendar.resource');
}
```

### 3. 服務類開發

#### 事件管理服務：CalendarEventService.php

```php
<?php

namespace App\Services\Calendar;

use App\Models\SchoolEvent;
use App\Models\Course;
use App\Models\Campus;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CalendarEventService
{
    /**
     * 獲取行事曆事件
     */
    public function getEvents(FetchInfo $info, array $filters = []): Collection|array|Builder
    {
        $query = collect()
            ->push(...$this->getSchoolEvents($info, $filters))
            ->push(...$this->getCourses($info, $filters));

        return $this->applyFilters($query, $filters);
    }

    /**
     * 獲取校務事件
     */
    protected function getSchoolEvents(FetchInfo $info, array $filters = []): Collection
    {
        $query = SchoolEvent::query()
            ->where('status', 'active')
            ->whereDate('start_time', '>=', $info->start)
            ->whereDate('start_time', '<=', $info->end)
            ->with('campus');

        // 應用校區篩選
        if (isset($filters['campus_id'])) {
            $query->where('campus_id', $filters['campus_id']);
        }

        return $query->get();
    }

    /**
     * 獲取課程事件
     */
    protected function getCourses(FetchInfo $info, array $filters = []): Collection
    {
        $query = Course::query()
            ->where('is_active', true)
            ->whereDate('start_time', '>=', $info->start)
            ->whereDate('start_time', '<=', $info->end)
            ->with('campus');

        // 應用校區篩選
        if (isset($filters['campus_id'])) {
            $query->where('campus_id', $filters['campus_id']);
        }

        return $query->get();
    }

    /**
     * 獲取行事曆資源（校區）
     */
    public function getResources(): Collection|array|Builder
    {
        return Campus::query()
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * 應用篩選器
     */
    protected function applyFilters(Collection $events, array $filters): Collection
    {
        // 實現篩選邏輯
        return $events;
    }
}
```

#### 視圖管理服務：CalendarViewService.php

```php
<?php

namespace App\Services\Calendar;

class CalendarViewService
{
    /**
     * 獲取行事曆選項（Guava Calendar 方式）
     */
    public function getOptions(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek,resourceTimeGridWeek'
            ],
            'buttonText' => [
                'today' => '今天',
                'month' => '月',
                'week' => '週',
                'day' => '日',
                'list' => '列表',
                'resourceTimeGridWeek' => '資源週'
            ],
            'resourceAreaWidth' => '20%',
            'resourceLabelText' => '校區',
        ];
    }

    /**
     * 切換視圖
     */
    public function switchView(string $view): void
    {
        // 實現視圖切換邏輯
    }

    /**
     * 檢測是否為行動裝置
     */
    public function isMobileDevice(): bool
    {
        return request()->header('User-Agent') &&
               preg_match('/Mobile|Android|iPhone|iPad/', request()->header('User-Agent'));
    }
}
```

#### 動作管理服務：CalendarActionService.php

```php
<?php

namespace App\Services\Calendar;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Guava\Calendar\ValueObjects\EventClickInfo;
use Guava\Calendar\ValueObjects\EventDropInfo;
use Guava\Calendar\ValueObjects\EventResizeInfo;

class CalendarActionService
{
    /**
     * 獲取標題動作
     */
    public function getHeaderActions(): array
    {
        return [
            $this->createEventAction(),
            $this->filterAction(),
            $this->exportAction(),
        ];
    }

    /**
     * 創建事件動作
     */
    protected function createEventAction(): Action
    {
        return Action::make('createEvent')
            ->label('新增事件')
            ->icon('heroicon-o-plus')
            ->form([
                Select::make('type')
                    ->label('事件類型')
                    ->options([
                        'school_event' => '校務活動',
                        'course' => '課程',
                    ])
                    ->required(),
                TextInput::make('title')
                    ->label('標題')
                    ->required(),
                DateTimePicker::make('start_time')
                    ->label('開始時間')
                    ->required(),
                DateTimePicker::make('end_time')
                    ->label('結束時間'),
            ])
            ->action(function (array $data) {
                // 實現創建事件邏輯
                Notification::make()
                    ->title('事件創建成功')
                    ->success()
                    ->send();
            });
    }

    /**
     * 篩選動作
     */
    protected function filterAction(): Action
    {
        return Action::make('filter')
            ->label('篩選')
            ->icon('heroicon-o-funnel')
            ->form([
                Select::make('campus_id')
                    ->label('校區')
                    ->options(Campus::pluck('name', 'id'))
                    ->placeholder('選擇校區'),
            ])
            ->action(function (array $data) {
                // 實現篩選邏輯
            });
    }

    /**
     * 匯出動作
     */
    protected function exportAction(): Action
    {
        return Action::make('export')
            ->label('匯出')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () {
                // 實現匯出邏輯
            });
    }

    /**
     * 處理事件點擊
     */
    public function handleEventClick(EventClickInfo $info, $event, ?string $action = null): void
    {
        $calendarEvent = $info->event;
        $extendedProps = $calendarEvent->extendedProps ?? [];

        $title = $calendarEvent->title ?? '無標題';
        $type = $this->getEventTypeLabel($extendedProps['type'] ?? '');
        $campus = $extendedProps['campus'] ?? '未知校區';

        Notification::make()
            ->title('事件詳情')
            ->body("標題：{$title}\n類型：{$type}\n校區：{$campus}")
            ->info()
            ->send();
    }

    /**
     * 處理事件拖放
     */
    public function handleEventDrop(EventDropInfo $info, $event): bool
    {
        $calendarEvent = $info->event;
        $newStart = $calendarEvent->getStart()->format('Y-m-d H:i');

        // 更新資料庫中的事件時間
        $this->updateEventTime($calendarEvent, $newStart, null);

        Notification::make()
            ->title('事件移動')
            ->body("事件已移動到 {$newStart}")
            ->success()
            ->send();

        return true;
    }

    /**
     * 處理事件調整大小
     */
    public function handleEventResize(EventResizeInfo $info, $event): bool
    {
        $calendarEvent = $info->event;
        $newEnd = $calendarEvent->getEnd()->format('Y-m-d H:i');

        // 更新資料庫中的事件結束時間
        $this->updateEventTime($calendarEvent, null, $newEnd);

        Notification::make()
            ->title('事件調整')
            ->body("事件已調整結束時間到 {$newEnd}")
            ->success()
            ->send();

        return true;
    }

    /**
     * 更新事件時間
     */
    protected function updateEventTime($calendarEvent, ?string $newStart, ?string $newEnd): void
    {
        $extendedProps = $calendarEvent->extendedProps ?? [];
        $type = $extendedProps['type'] ?? '';
        $modelId = $extendedProps['model_id'] ?? null;

        if (!$modelId) return;

        try {
            switch ($type) {
                case 'school_event':
                    $event = SchoolEvent::find($modelId);
                    if ($event) {
                        if ($newStart) $event->start_time = $newStart;
                        if ($newEnd) $event->end_time = $newEnd;
                        $event->save();
                    }
                    break;
                case 'course':
                    $course = Course::find($modelId);
                    if ($course) {
                        if ($newStart) $course->start_time = $newStart;
                        if ($newEnd) $course->end_time = $newEnd;
                        $course->save();
                    }
                    break;
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('更新失敗')
                ->body('無法更新事件時間：' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * 獲取事件類型標籤
     */
    protected function getEventTypeLabel(string $type): string
    {
        return match ($type) {
            'school_event' => '校務活動',
            'course' => '課程',
            default => '未知類型',
        };
    }
}
```

### 3. Trait 開發

#### 事件管理 Trait：HasCalendarEvents.php

```php
<?php

namespace App\Traits;

use App\Services\Calendar\CalendarEventService;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait HasCalendarEvents
{
    protected CalendarEventService $eventService;

    /**
     * 初始化事件服務
     */
    protected function initializeEventService(): void
    {
        $this->eventService = app(CalendarEventService::class);
    }

    /**
     * 獲取行事曆事件
     */
    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        $this->initializeEventService();
        return $this->eventService->getEvents($info, $this->getFilters());
    }

    /**
     * 獲取行事曆資源
     */
    protected function getResources(): Collection|array|Builder
    {
        $this->initializeEventService();
        return $this->eventService->getResources();
    }
}
```

#### 動作管理 Trait：HasCalendarActions.php

```php
<?php

namespace App\Traits;

use App\Services\Calendar\CalendarActionService;
use Filament\Actions\Action;

trait HasCalendarActions
{
    protected CalendarActionService $actionService;

    /**
     * 初始化動作服務
     */
    protected function initializeActionService(): void
    {
        $this->actionService = app(CalendarActionService::class);
    }

    /**
     * 獲取標題動作
     */
    protected function getHeaderActions(): array
    {
        $this->initializeActionService();
        return $this->actionService->getHeaderActions();
    }

    /**
     * 獲取所有動作
     */
    public function getActions(): array
    {
        return $this->getHeaderActions();
    }
}
```

#### 視圖管理 Trait：HasCalendarViews.php

```php
<?php

namespace App\Traits;

use App\Services\Calendar\CalendarViewService;

trait HasCalendarViews
{
    protected CalendarViewService $viewService;

    /**
     * 初始化視圖服務
     */
    protected function initializeViewService(): void
    {
        $this->viewService = app(CalendarViewService::class);
    }

    /**
     * 獲取行事曆選項
     */
    public function getOptions(): array
    {
        $this->initializeViewService();
        return $this->viewService->getOptions();
    }

    /**
     * 切換視圖
     */
    public function switchView(string $view): void
    {
        $this->initializeViewService();
        $this->viewService->switchView($view);
    }
}
```

#### 篩選管理 Trait：HasCalendarFilters.php

```php
<?php

namespace App\Traits;

trait HasCalendarFilters
{
    public array $filters = [];

    /**
     * 獲取篩選器
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * 設定篩選器
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * 清除篩選器
     */
    public function clearFilters(): void
    {
        $this->filters = [];
    }
}
```

### 4. 使用現有模型

**✅ 重要：專案中已存在完整的模型檔案，無需重新建立！**

現有的模型檔案已經正確實現了 Guava Calendar 所需的所有接口：

#### 現有模型檔案

| 模型檔案          | 接口實現       | 功能描述                             |
| ----------------- | -------------- | ------------------------------------ |
| `SchoolEvent.php` | `Eventable`    | 校務活動，已實現 `toCalendarEvent()` |
| `Course.php`      | `Eventable`    | 課程，已實現 `toCalendarEvent()`     |
| `Campus.php`      | `Resourceable` | 校區，已實現 `toCalendarResource()`  |

**所有模型都已正確實現 Guava Calendar 接口，可直接使用！**

#### 模型接口確認

```php
// SchoolEvent.php - 已實現 Eventable 接口
class SchoolEvent extends Model implements Eventable
{
    public function toCalendarEvent(): CalendarEvent
    {
        return CalendarEvent::make()
            ->key('event_' . $this->id)
            ->title($this->title)
            ->start($this->start_time)
            ->end($this->end_time ?? $this->start_time->addHours(2))
            ->backgroundColor($this->campus?->color ?? '#3B82F6') // 使用校區顏色
            ->textColor('#ffffff')
            ->resourceId('campus_' . $this->campus_id)
            ->extendedProps([
                'model' => static::class,
                'key' => 'event_' . $this->id,
                'type' => 'school_event',
                'model_id' => $this->id,
                // ... 其他屬性
            ]);
    }
}

// Course.php - 已實現 Eventable 接口
class Course extends Model implements Eventable
{
    public function toCalendarEvent(): CalendarEvent
    {
        return CalendarEvent::make()
            ->key('course_' . $this->id)
            ->title($this->name)
            ->start($this->start_time)
            ->end($this->end_time ?? $this->start_time->addHours(1))
            ->backgroundColor($this->campus?->color ?? '#3B82F6') // 使用校區顏色
            ->textColor('#ffffff')
            ->resourceId('campus_' . $this->campus_id)
            ->extendedProps([
                'model' => static::class,
                'key' => 'course_' . $this->id,
                'type' => 'course',
                'model_id' => $this->id,
                // ... 其他屬性
            ]);
    }
}

// Campus.php - 已實現 Resourceable 接口
class Campus extends Model implements Resourceable
{
    public function toCalendarResource(): CalendarResource
    {
        return CalendarResource::make('campus_' . $this->id)
            ->title($this->name)
            ->eventBackgroundColor($this->color ?? '#3B82F6') // 校區顏色
            ->extendedProps([
                'campus_id' => $this->id,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
            ]);
    }
}
```

#### 使用現有模型的好處

1. **無需重複開發**：所有必要的接口都已實現
2. **符合專案架構**：與現有的 Filament 資源完全整合
3. **資料一致性**：使用相同的模型確保資料一致性
4. **維護便利**：只需維護一套模型檔案

---

## 🎨 行事曆顯示配置

### 1. 全域行事曆顯示

在管理後台首頁 (`/admin`) 顯示所有校區的行事曆：

-   **校務活動**：顯示在對應校區的資源列中，使用 `SchoolEvent` 模型
-   **課程資訊**：顯示在對應校區的資源列中，使用 `Course` 模型
-   **校區顏色**：所有事件都使用所屬校區的 `color` 欄位作為背景色
-   **資源分組**：每個校區作為一個資源列，方便區分不同校區的事件
-   **篩選功能**：可以透過校區篩選器選擇特定校區的事件

### 2. 校區專用行事曆顯示

在校區管理頁面 (`/admin/campuses/{id}`) 只顯示該校區的事件：

-   **單一校區**：只顯示當前校區的校務活動和課程
-   **校區顏色**：所有事件都使用該校區的 `color` 欄位作為背景色
-   **無資源分組**：因為只有一個校區，不需要資源列分組
-   **完整功能**：保留所有互動功能（拖放、調整大小、點擊等）

### 3. 校區顏色配置

校區顏色在 `Campus` 模型中設定：

```php
// 在 Campus 模型中實現 Resourceable 接口
public function toCalendarResource(): CalendarResource
{
    return CalendarResource::make('campus_' . $this->id)
        ->title($this->name)
        ->eventBackgroundColor($this->color ?? '#3B82F6') // 使用校區顏色
        ->extendedProps([
            'campus_id' => $this->id,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
        ]);
}
```

### 4. 資源顯示設定

-   **校區資源**：每個活躍校區作為一個資源列，使用 `Campus` 模型
-   **自動排序**：根據校區的 `sort_order` 欄位排序
-   **狀態過濾**：只顯示 `is_active = true` 的校區
-   **資料整合**：與現有的校區管理資源完全整合

---

## 📊 重構效果分析

### 1. 檔案大小控制

| 檔案類型      | 檔案名稱                  | 預估行數   | 職責範圍                 |
| ------------- | ------------------------- | ---------- | ------------------------ |
| **主 Widget** | CalendarWidget.php        | 200-250 行 | 核心行事曆功能和互動處理 |
| **服務類**    | CalendarEventService.php  | 100-150 行 | 事件和資源查詢管理       |
| **服務類**    | CalendarViewService.php   | 50-100 行  | 視圖配置和行動裝置檢測   |
| **服務類**    | CalendarActionService.php | 150-200 行 | 動作定義和事件處理       |
| **Trait**     | HasCalendarEvents.php     | 20-30 行   | 事件管理方法封裝         |
| **Trait**     | HasCalendarActions.php    | 20-30 行   | 動作管理方法封裝         |
| **Trait**     | HasCalendarViews.php      | 15-25 行   | 視圖管理方法封裝         |
| **Trait**     | HasCalendarFilters.php    | 10-20 行   | 篩選管理方法封裝         |

### 2. 符合規範檢查

#### ✅ SDUI 原則

-   **結構化配置**：所有 UI 通過 Action 和 Form 結構化配置
-   **後端驅動**：無複雜前端模板，依賴 PHP 配置對象
-   **簡潔性**：每個檔案職責單一，易於維護

#### ✅ Filament 4.x 規範

-   **標準方法**：實現 `getHeaderActions()` 和 `getNavigationLabel()`
-   **組件整合**：使用 `CreateAction`、`EditAction` 等標準元件
-   **模組化設計**：使用 Trait 組織功能，符合 Filament 慣例

#### ✅ Guava Calendar 規範

-   **核心方法**：保留 `getEvents()`、`getResources()` 和 `getOptions()`
-   **互動回呼**：實現 `onEventClick`、`onEventDrop` 等必要方法
-   **資料格式**：正確返回 `CalendarEvent` 和 `CalendarResource`

### 3. 重構優勢

#### 可維護性提升

-   **單一職責**：每個服務類專注於特定功能
-   **低耦合**：服務類之間依賴關係清晰
-   **高內聚**：相關功能集中在同一服務類中

#### 可擴展性增強

-   **新功能添加**：可輕鬆新增服務類或 Trait
-   **功能修改**：修改特定功能不影響其他模組
-   **測試友好**：每個服務類可獨立測試

#### 效能優化

-   **延遲載入**：服務類按需初始化
-   **查詢優化**：事件查詢邏輯集中管理
-   **快取支援**：可在服務類中實現快取機制

---

## 🔐 權限管理

### 1. 行事曆權限控制

```php
// 在 CalendarWidget 中
public function canCreate(): bool
{
    return auth()->user()->can('create', SchoolEvent::class) ||
           auth()->user()->can('create', Course::class);
}

public function canEdit(): bool
{
    return auth()->user()->can('update', SchoolEvent::class) ||
           auth()->user()->can('update', Course::class);
}

public function canDelete(): bool
{
    return auth()->user()->can('delete', SchoolEvent::class) ||
           auth()->user()->can('delete', Course::class);
}
```

### 2. 動作權限控制

```php
// 在動作方法中
public function createEventAction(): CreateAction
{
    return $this->createAction(SchoolEvent::class)
        ->authorize('create', SchoolEvent::class)
        ->authorizationNotification();
}
```

---

## 📊 資料填充

### 1. 現有範例資料 Seeder

**已根據業務模組創建完成！** 現有的 `CalendarSampleDataSeeder.php` 已經包含：

#### 校務活動範例資料

-   **開學典禮**：新學期開學典禮，歡迎所有學員
-   **舞蹈表演**：學員成果發表會
-   **家長座談會**：與家長討論學員學習狀況

#### 課程範例資料

-   **兒童芭蕾**：初級課程，價格 1200 元
-   **現代舞**：中級課程，價格 1500 元
-   **民族舞**：高級課程，價格 1800 元

#### 特色功能

-   **校區關聯**：所有事件都關聯到對應校區
-   **時間範圍**：2025 年 9 月 1 日至 11 月 30 日
-   **工作時間**：上午 9 點至下午 6 點
-   **隨機生成**：時間和學員數量隨機生成

### 2. 執行資料填充

```bash
# 執行行事曆範例資料填充
php artisan db:seed --class=CalendarSampleDataSeeder
```

### 3. 資料結構說明

```php
// 校務活動資料結構
SchoolEvent::create([
    'title' => $campus->name . ' - 開學典禮',
    'description' => '新學期開學典禮，歡迎所有學員',
    'start_time' => $startTime,
    'end_time' => $endTime,
    'location' => $campus->name . ' 大廳',
    'category' => 'meeting',
    'status' => 'active',
    'campus_id' => $campus->id,
    'created_by' => 1,
]);

// 課程資料結構
Course::create([
    'name' => $campus->name . ' - 兒童芭蕾',
    'description' => '兒童芭蕾課程',
    'start_time' => $startTime,
    'end_time' => $endTime,
    'level' => 'beginner',
    'price' => 1200,
    'student_count' => rand(8, 20),
    'is_active' => true,
    'campus_id' => $campus->id,
]);
```

---

## 🎛️ 行事曆配置

### 1. 視圖類型配置

```php
// 可用的行事曆視圖類型
use Guava\Calendar\Enums\CalendarViewType;

// 月視圖
protected CalendarViewType $calendarView = CalendarViewType::DayGridMonth;

// 週視圖
protected CalendarViewType $calendarView = CalendarViewType::TimeGridWeek;

// 日視圖
protected CalendarViewType $calendarView = CalendarViewType::TimeGridDay;

// 列表視圖
protected CalendarViewType $calendarView = CalendarViewType::ListWeek;

// 資源時間網格週視圖
protected CalendarViewType $calendarView = CalendarViewType::ResourceTimeGridWeek;
```

### 2. 互動功能配置

```php
// 啟用/禁用各種互動功能
protected bool $eventClickEnabled = true;        // 事件點擊
protected bool $dateClickEnabled = true;         // 日期點擊
protected bool $dateSelectEnabled = true;        // 日期選擇
protected bool $eventDragEnabled = true;         // 事件拖放
protected bool $eventResizeEnabled = true;       // 事件調整大小
protected bool $viewDidMountEnabled = true;      // 視圖載入完成
protected bool $datesSetEnabled = true;          // 日期範圍變更
protected bool $noEventsClickEnabled = true;     // 無事件區域點擊
```

### 3. Guava Calendar 屬性配置

**✅ 正確方式：使用 Guava Calendar 屬性**

```php
use Carbon\WeekDay;

class CalendarWidget extends BaseCalendarWidget
{
    // 設定週日為第一天（Guava Calendar 方式）
    protected WeekDay $firstDay = WeekDay::Sunday;

    // 設定本地化（Guava Calendar 方式）
    protected ?string $locale = 'zh-tw';

    // 設定每天最大事件數（Guava Calendar 方式）
    protected bool $dayMaxEvents = true;

    // 使用 Filament 時區（Guava Calendar 方式）
    protected bool $useFilamentTimezone = true;

    // 其他配置...
}
```

**❌ 錯誤方式：使用 FullCalendar 配置**

```php
// 不要這樣做
public function getOptions(): array
{
    return [
        'firstDay' => 0,           // 這是 FullCalendar 方式
        'locale' => 'zh-tw',       // 這是 FullCalendar 方式
        'dayMaxEvents' => true,    // 這是 FullCalendar 方式
    ];
}
```

### 4. 本地化配置

**✅ 正確方式：使用 Guava Calendar 屬性**

```php
use Carbon\WeekDay;

class CalendarWidget extends BaseCalendarWidget
{
    // 設定本地化（Guava Calendar 方式）
    protected ?string $locale = 'zh-tw';

    // 設定週日為第一天（Guava Calendar 方式）
    protected WeekDay $firstDay = WeekDay::Sunday;
}
```

**❌ 錯誤方式：使用 FullCalendar 配置**

```php
// 不要這樣做
public function getOptions(): array
{
    return [
        'locale' => 'zh-tw', // 這是 FullCalendar 方式
        'firstDay' => 0,     // 這是 FullCalendar 方式
    ];
}
```

---

## 🔄 事件處理

### 1. 事件點擊處理

```php
protected function onEventClick(EventClickInfo $info, Model $event, ?string $action = null): void
{
    $calendarEvent = $info->event;
    $extendedProps = $calendarEvent->extendedProps ?? [];

    // 根據事件類型執行不同操作
    switch ($extendedProps['type']) {
        case 'school_event':
            $this->handleSchoolEventClick($calendarEvent);
            break;
        case 'course':
            $this->handleCourseClick($calendarEvent);
            break;
    }
}

private function handleSchoolEventClick($calendarEvent): void
{
    // 校務事件點擊處理邏輯
    Notification::make()
        ->title('校務活動詳情')
        ->body($calendarEvent->title)
        ->info()
        ->send();
}
```

### 2. 日期選擇處理

```php
protected function onDateSelect(DateSelectInfo $info): void
{
    $start = $info->start->format('Y-m-d H:i');
    $end = $info->end->format('Y-m-d H:i');

    // 顯示選擇的時間範圍資訊
    Notification::make()
        ->title('日期選擇')
        ->body("選擇時間範圍：{$start} 至 {$end}")
        ->info()
        ->send();
}
```

### 3. 事件拖放處理

```php
protected function onEventDrop(EventDropInfo $info, Model $event): bool
{
    $calendarEvent = $info->event;
    $newStart = $calendarEvent->getStart();
    $newEnd = $calendarEvent->getEnd();

    try {
        // 更新資料庫
        $this->updateEventTime($calendarEvent, $newStart, $newEnd);

        Notification::make()
            ->title('事件移動成功')
            ->success()
            ->send();

        return true;
    } catch (\Exception $e) {
        Notification::make()
            ->title('事件移動失敗')
            ->body($e->getMessage())
            ->danger()
            ->send();

        return false;
    }
}
```

---

## 🎨 樣式配置

### 1. 校區顏色系統

**重要：所有事件都使用所屬校區的顏色，課程和校務活動本身沒有顏色設定！**

```php
// 校區顏色配置（在 Campus 模型中）
public function toCalendarResource(): CalendarResource
{
    return CalendarResource::make('campus_' . $this->id)
        ->title($this->name)
        ->eventBackgroundColor($this->color ?? '#3B82F6') // 校區專屬顏色
        ->extendedProps([
            'campus_id' => $this->id,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
        ]);
}

// 校務活動使用校區顏色（在 SchoolEvent 模型中）
public function toCalendarEvent(): CalendarEvent
{
    return CalendarEvent::make()
        ->key('event_' . $this->id)
        ->title($this->title)
        ->start($this->start_time)
        ->end($this->end_time ?? $this->start_time->addHours(2))
        ->backgroundColor($this->campus?->color ?? '#3B82F6') // 使用所屬校區顏色
        ->textColor('#ffffff')
        ->resourceId('campus_' . $this->campus_id)
        ->extendedProps([
            'type' => 'school_event',
            'model_id' => $this->id,
            'description' => $this->description,
            'campus' => $this->campus?->name,
            'location' => $this->location,
            'category' => $this->category,
            'status' => $this->status,
        ]);
}

// 課程使用校區顏色（在 Course 模型中）
public function toCalendarEvent(): CalendarEvent
{
    return CalendarEvent::make()
        ->key('course_' . $this->id)
        ->title($this->name)
        ->start($this->start_time)
        ->end($this->end_time ?? $this->start_time->addHours(1))
        ->backgroundColor($this->campus?->color ?? '#3B82F6') // 使用所屬校區顏色
        ->textColor('#ffffff')
        ->resourceId('campus_' . $this->campus_id)
        ->extendedProps([
            'type' => 'course',
            'model_id' => $this->id,
            'description' => $this->description,
            'campus' => $this->campus?->name,
            'level' => $this->level,
            'price' => $this->price,
            'student_count' => $this->student_count,
        ]);
}
```

### 2. 顏色管理原則

-   **校區顏色**：每個校區在管理後台設定專屬顏色
-   **事件顏色**：所有事件（校務活動、課程）都使用所屬校區的顏色
-   **無個別顏色**：課程和校務活動本身沒有顏色設定，完全依賴校區顏色
-   **一致性**：同一校區的所有事件使用相同顏色，便於識別
-   **預設顏色**：如果校區未設定顏色，使用預設藍色 `#3B82F6`

### 3. 預設樣式

行事曆使用 Filament 4.x 預設樣式，自動適應主題：

-   **響應式設計**：自動適應不同螢幕尺寸
-   **主題整合**：與 Filament 主題完美整合
-   **無障礙支援**：符合無障礙設計標準

---

## 🧪 測試

### 1. 功能測試

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\SchoolEvent;
use App\Models\Course;
use App\Models\Campus;
use Carbon\Carbon;

class CalendarTest extends TestCase
{
    public function test_calendar_displays_events()
    {
        // 創建測試資料
        $campus = Campus::factory()->create();
        $event = SchoolEvent::factory()->create([
            'campus_id' => $campus->id,
            'start_time' => now(),
            'end_time' => now()->addHours(2),
        ]);

        // 測試行事曆是否顯示事件
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee($event->title);
    }

    public function test_event_drag_and_drop()
    {
        $event = SchoolEvent::factory()->create();
        $newStart = now()->addDay();

        // 模擬拖放操作
        $response = $this->post('/admin/calendar/event-drop', [
            'event_id' => $event->id,
            'new_start' => $newStart,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('school_events', [
            'id' => $event->id,
            'start_time' => $newStart,
        ]);
    }
}
```

### 2. 權限測試

```php
public function test_calendar_permissions()
{
    $user = User::factory()->create();
    $user->givePermissionTo('view calendar');

    $this->actingAs($user)
         ->get('/admin/calendar')
         ->assertStatus(200);

    $user->revokePermissionTo('view calendar');

    $this->actingAs($user)
         ->get('/admin/calendar')
         ->assertStatus(403);
}
```

---

## 🚀 部署與維護

### 1. 生產環境配置

```php
// config/calendar.php
return [
    'cache_events' => env('CALENDAR_CACHE_EVENTS', true),
    'cache_ttl' => env('CALENDAR_CACHE_TTL', 3600),
    'max_events_per_request' => env('CALENDAR_MAX_EVENTS', 1000),
];
```

### 2. 效能優化

```php
// 在 CalendarWidget 中優化查詢
protected function getEvents(FetchInfo $info): Collection|array|Builder
{
    return collect()
        ->push(...SchoolEvent::query()
            ->where('status', 'active')
            ->whereDate('start_time', '>=', $info->start)
            ->whereDate('start_time', '<=', $info->end)
            ->with('campus') // 預載入關聯
            ->select(['id', 'title', 'start_time', 'end_time', 'campus_id', 'category']) // 只選擇需要的欄位
            ->get())
        ->push(...Course::query()
            ->where('is_active', true)
            ->whereDate('start_time', '>=', $info->start)
            ->whereDate('start_time', '<=', $info->end)
            ->with('campus')
            ->select(['id', 'name', 'start_time', 'end_time', 'campus_id', 'level'])
            ->get());
}
```

### 3. 快取策略

```php
// 使用快取優化效能
protected function getEvents(FetchInfo $info): Collection|array|Builder
{
    $cacheKey = 'calendar_events_' . $info->start->format('Y-m-d') . '_' . $info->end->format('Y-m-d');

    return Cache::remember($cacheKey, 3600, function () use ($info) {
        return $this->fetchEventsFromDatabase($info);
    });
}
```

---

## 📚 參考資源

### 官方文檔

-   [Guava Calendar 官方文檔](https://filamentphp.com/plugins/guava-calendar)
-   [Filament 4.x 官方文檔](https://filamentphp.com/docs)
-   [Laravel 12.x 官方文檔](https://laravel.com/docs)

### 專案相關

-   模型關係：`app/Models/`
-   Filament 資源：`app/Filament/Resources/`
-   資料庫結構：`database/migrations/`
-   行事曆 Widget：`app/Filament/Widgets/CalendarWidget.php`

---

## 🔄 版本更新

### 更新記錄

| 版本 | 日期       | 更新內容                                 |
| ---- | ---------- | ---------------------------------------- |
| 1.0  | 2025-01-15 | 初始版本，基本行事曆功能                 |
| 1.1  | 2025-01-16 | 新增事件拖放功能                         |
| 1.2  | 2025-01-17 | 移除自訂義，專注現有功能                 |
| 1.3  | 2025-01-17 | 新增校區篩選和校區顏色功能               |
| 1.4  | 2025-01-17 | 重構架構，符合 SDUI 和 Filament 4.x 規範 |

### 未來規劃

-   [ ] 重複事件支援
-   [ ] 事件提醒功能
-   [ ] 行事曆匯出功能
-   [ ] 行動裝置優化
-   [ ] 與現有 Filament 資源深度整合

---

## ⚠️ 注意事項

### 開發限制

-   ❌ 不使用 CDN 資源
-   ❌ 不修改 Filament 核心檔案
-   ❌ 不引入不必要的第三方套件
-   ❌ **不使用英文註解**

### 效能考量

-   大量事件時使用分頁載入
-   適當使用資料庫索引
-   實作事件快取機制
-   避免 N+1 查詢問題

### 安全性

-   驗證所有用戶輸入
-   實作適當的權限控制
-   防止 XSS 攻擊
-   使用 CSRF 保護

---

**📌 重要提醒：每次開發前必須重新閱讀此文檔，確保遵循所有規範！**

---

_最後更新：2025 年 1 月 17 日_  
_版本：1.4 - Guava Calendar 2.x + Filament 4.x + 重構架構 + SDUI 規範_
