<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Course;
use App\Models\Campus;
use App\Models\SchoolEvent;
use App\Models\Attendance;
use App\Models\Equipment;
use App\Models\Finance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShowcaseStatsWidget extends BaseWidget
{
    // 設定Widget標題
    protected ?string $heading = '系統統計概覽';

    // 設定Widget描述
    protected ?string $description = '舞蹈工作室管理系統的關鍵數據統計';

    protected function getStats(): array
    {
        return [
            Stat::make('總用戶數', User::count())
                ->description('系統註冊的總用戶數量')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('課程數量', Course::count())
                ->description('可用的舞蹈課程總數')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([17, 16, 14, 15, 14, 13, 12]),

            Stat::make('校區數量', Campus::count())
                ->description('舞蹈工作室的校區總數')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('warning'),

            Stat::make('活動數量', SchoolEvent::count())
                ->description('校務活動和課程活動總數')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('出勤記錄', Attendance::count())
                ->description('學員出勤記錄總數')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),

            Stat::make('設備數量', Equipment::count())
                ->description('舞蹈設備和器材總數')
                ->descriptionIcon('heroicon-m-cog')
                ->color('gray'),

            Stat::make('財務記錄', Finance::count())
                ->description('財務收支記錄總數')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('danger'),
        ];
    }
}
