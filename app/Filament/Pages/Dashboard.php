<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = '主控板';


    protected function getHeaderWidgets(): array
    {
        return [
            // 統計卡片放在上方
            \Filament\Widgets\AccountWidget::class,
            \Filament\Widgets\FilamentInfoWidget::class,
            \App\Filament\Widgets\ShowcaseStatsWidget::class,
            \App\Filament\Widgets\CalendarStatsWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            // 行事曆 Widget
            \App\Filament\Widgets\CalendarWidget::class,
        ];
    }

    // 設定 Dashboard 為單欄佈局，確保行事曆滿版顯示
    public function getColumns(): int | array
    {
        return 1;
    }
}
