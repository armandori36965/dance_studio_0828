<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 強制設定系統時區為台北時間，解決時區不一致問題
        date_default_timezone_set('Asia/Taipei');

        // 確保Laravel時區配置一致
        config(['app.timezone' => 'Asia/Taipei']);
        // 設定應用程式語言為繁體中文
        $this->app->setLocale('zh_TW');
    }
}
