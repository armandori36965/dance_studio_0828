<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class CalendarAssetServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 檢查是否使用本地資源（避免 CDN 載入問題）
        if (config('app.use_local_calendar_assets', true)) {
            // 覆蓋 Guava Calendar 的 CDN 資源，使用本地文件
            FilamentAsset::register(
                assets: [
                    // 覆蓋 Guava Calendar 的 CSS 資源
                    Css::make('calendar-styles', public_path('vendor/event-calendar/event-calendar.min.css')),
                    // 覆蓋 Guava Calendar 的 JS 資源
                    Js::make('calendar-script', public_path('vendor/event-calendar/event-calendar.min.js')),
                ],
                package: 'guava/calendar'
            );
        }
    }
}
