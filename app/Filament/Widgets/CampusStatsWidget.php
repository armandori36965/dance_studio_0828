<?php

namespace App\Filament\Widgets;

use App\Models\Campus;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CampusStatsWidget extends StatsOverviewWidget
{
    public ?Campus $campus = null;

    // 只在特定頁面顯示
    public static function canView(): bool
    {
        // 檢查是否在校區 Dashboard 頁面
        return request()->routeIs('filament.admin.resources.campuses.view') &&
               request()->route('record');
    }

    protected function getStats(): array
    {
        // 獲取校區記錄
        $campus = $this->campus;

        if (!$campus) {
            return [];
        }

        // 使用快取提升效能
        return cache()->remember("campus_stats_{$campus->id}", 600, function () use ($campus) {
            return [
            // 校務事件數量
            Stat::make('校務事件', $campus->schoolEvents()->count())
                ->description('總事件數量')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart($this->getEventChartData($campus)),

            // 課程數量
            Stat::make('課程數量', $campus->courses()->count())
                ->description('總課程數量')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart($this->getCourseChartData($campus)),

            // 人員數量
            Stat::make('人員數量', $campus->users()->count())
                ->description('總人員數量')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart($this->getUserChartData($campus)),

            // 設備數量
            Stat::make('設備數量', $campus->equipment()->count())
                ->description('總設備數量')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('warning')
                ->chart($this->getEquipmentChartData($campus)),
            ];
        });
    }

    // 校務事件線圖數據（過去7天）
    protected function getEventChartData($campus): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = $campus->schoolEvents()
                ->whereDate('start_time', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    // 課程線圖數據（過去7天）
    protected function getCourseChartData($campus): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = $campus->courses()
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    // 人員線圖數據（過去7天）
    protected function getUserChartData($campus): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = $campus->users()
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    // 設備線圖數據（過去7天）
    protected function getEquipmentChartData($campus): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = $campus->equipment()
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
