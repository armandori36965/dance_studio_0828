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
        // 註冊本地 Event Calendar 資源
        FilamentAsset::register(
            assets: [
                // 使用本地 CSS 檔案
                Css::make('event-calendar-styles', public_path('vendor/event-calendar/event-calendar.min.css')),
                // 使用本地 JS 檔案
                Js::make('event-calendar-script', public_path('vendor/event-calendar/event-calendar.min.js')),
            ],
            package: 'app/calendar'
        );
    }
}
