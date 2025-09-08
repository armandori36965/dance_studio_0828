<?php

namespace App\Filament\Widgets;

use App\Models\Campus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CampusStatsWidget extends BaseWidget
{
    public ?Campus $campus = null;

    protected function getStats(): array
    {
        // 嘗試從多個來源獲取校區記錄
        $campus = $this->campus;

        if (!$campus) {
            $campusId = request()->route('record');
            $campus = Campus::find($campusId);
        }

        if (!$campus) {
            return [
                Stat::make('載入中...', '')
                    ->description('正在載入校區數據')
                    ->color('gray'),
            ];
        }

        return [
            // 校務事件數量
            Stat::make('校務事件', $campus->schoolEvents()->count())
                ->description('總事件數量')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            // 課程數量
            Stat::make('課程數量', $campus->courses()->count())
                ->description('總課程數量')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            // 人員數量
            Stat::make('人員數量', $campus->users()->count())
                ->description('總人員數量')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            // 設備數量
            Stat::make('設備數量', $campus->equipment()->count())
                ->description('總設備數量')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('warning'),
        ];
    }
}
