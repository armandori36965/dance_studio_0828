<?php

namespace App\Filament\Widgets;

use App\Models\SchoolEvent;
use App\Models\Course;
use App\Models\Campus;
use App\Models\Attendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class CalendarStatsWidget extends BaseWidget
{
    // 設定Widget標題
    protected ?string $heading = '行事曆統計分析';

    // 設定Widget描述
    protected ?string $description = '行事曆事件和課程的詳細統計分析';

    protected function getStats(): array
    {
        // 使用快取提升效能
        return cache()->remember('calendar_stats', 300, function () {
            $now = now();
            $thisMonth = $now->copy()->startOfMonth();
            $lastMonth = $now->copy()->subMonth()->startOfMonth();

            return [
                // 本月事件統計
                Stat::make('本月校務活動', SchoolEvent::where('status', 'active')
                    ->whereBetween('start_time', [$thisMonth, $now->copy()->endOfMonth()])
                    ->count())
                    ->description('比上月 ' . $this->getEventGrowthPercentage('school_events') . '%')
                    ->descriptionIcon($this->getEventGrowthIcon('school_events'))
                    ->color('primary')
                    ->chart($this->getEventChartData('school_events')),

                Stat::make('本月課程', Course::where('is_active', true)
                    ->whereBetween('start_time', [$thisMonth, $now->copy()->endOfMonth()])
                    ->count())
                    ->description('比上月 ' . $this->getEventGrowthPercentage('courses') . '%')
                    ->descriptionIcon($this->getEventGrowthIcon('courses'))
                    ->color('success')
                    ->chart($this->getEventChartData('courses')),

                Stat::make('本月出勤記錄', Attendance::whereBetween('date', [$thisMonth, $now->copy()->endOfMonth()])
                    ->count())
                    ->description('比上月 ' . $this->getEventGrowthPercentage('attendances') . '%')
                    ->descriptionIcon($this->getEventGrowthIcon('attendances'))
                    ->color('info')
                    ->chart($this->getEventChartData('attendances')),

                // 校區統計
                Stat::make('活躍校區', Campus::where('is_active', true)->count())
                    ->description('總校區數')
                    ->descriptionIcon('heroicon-m-building-office-2')
                    ->color('warning'),

                // 本週事件
                Stat::make('本週事件', $this->getThisWeekEventsCount())
                    ->description('包含所有類型事件')
                    ->descriptionIcon('heroicon-m-calendar-days')
                    ->color('gray'),

                // 即將到來的事件
                Stat::make('即將到來', $this->getUpcomingEventsCount())
                    ->description('未來7天內的事件')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('danger'),
            ];
        });
    }

    /**
     * 獲取事件增長百分比
     */
    protected function getEventGrowthPercentage(string $type): string
    {
        $now = now();
        $thisMonth = $now->copy()->startOfMonth();
        $lastMonth = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        $thisMonthCount = $this->getEventCountByType($type, $thisMonth, $now->copy()->endOfMonth());
        $lastMonthCount = $this->getEventCountByType($type, $lastMonth, $lastMonthEnd);

        if ($lastMonthCount == 0) {
            return $thisMonthCount > 0 ? '+100' : '0';
        }

        $growth = (($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
        return ($growth >= 0 ? '+' : '') . number_format($growth, 1);
    }

    /**
     * 獲取事件增長圖示
     */
    protected function getEventGrowthIcon(string $type): string
    {
        $growth = (float) str_replace(['+', '%'], '', $this->getEventGrowthPercentage($type));
        return $growth > 0 ? 'heroicon-m-arrow-trending-up' :
               ($growth < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus');
    }

    /**
     * 獲取事件數量
     */
    protected function getEventCountByType(string $type, Carbon $start, Carbon $end): int
    {
        return match ($type) {
            'school_events' => SchoolEvent::where('status', 'active')
                ->whereBetween('start_time', [$start, $end])
                ->count(),
            'courses' => Course::where('is_active', true)
                ->whereBetween('start_time', [$start, $end])
                ->count(),
            'attendances' => Attendance::whereBetween('date', [$start, $end])
                ->count(),
            default => 0,
        };
    }

    /**
     * 獲取事件圖表數據（過去7天）
     */
    protected function getEventChartData(string $type): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = $this->getEventCountByType($type,
                now()->subDays($i)->startOfDay(),
                now()->subDays($i)->endOfDay()
            );
            $data[] = $count;
        }
        return $data;
    }

    /**
     * 獲取本週事件數量
     */
    protected function getThisWeekEventsCount(): int
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $schoolEvents = SchoolEvent::where('status', 'active')
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->count();

        $courses = Course::where('is_active', true)
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->count();

        return $schoolEvents + $courses;
    }

    /**
     * 獲取即將到來的事件數量
     */
    protected function getUpcomingEventsCount(): int
    {
        $now = now();
        $nextWeek = $now->copy()->addDays(7);

        $schoolEvents = SchoolEvent::where('status', 'active')
            ->whereBetween('start_time', [$now, $nextWeek])
            ->count();

        $courses = Course::where('is_active', true)
            ->whereBetween('start_time', [$now, $nextWeek])
            ->count();

        return $schoolEvents + $courses;
    }
}
