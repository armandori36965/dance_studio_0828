<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 啟動 Laravel 應用
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/artisan',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 測試翻譯載入 ===\n";
echo "當前語言: " . app()->getLocale() . "\n";
echo "備用語言: " . config('app.fallback_locale') . "\n\n";

// 測試基本翻譯
echo "測試基本翻譯:\n";
echo "actions.add: " . __('actions.add') . "\n";
echo "fields.campus_name: " . __('filament-resources.fields.campus_name') . "\n";
echo "fields.address: " . __('filament-resources.fields.address') . "\n";
echo "fields.phone: " . __('filament-resources.fields.phone') . "\n";
echo "fields.email_address: " . __('filament-resources.fields.email_address') . "\n";
echo "fields.is_active: " . __('filament-resources.fields.is_active') . "\n\n";

// 測試 LocalizationHelper
echo "測試 LocalizationHelper:\n";
echo "field('campus_name'): " . \App\Helpers\LocalizationHelper::field('campus_name') . "\n";
echo "field('address'): " . \App\Helpers\LocalizationHelper::field('address') . "\n";
echo "field('phone'): " . \App\Helpers\LocalizationHelper::field('phone') . "\n";
echo "field('email_address'): " . \App\Helpers\LocalizationHelper::field('email_address') . "\n";
echo "field('is_active'): " . \App\Helpers\LocalizationHelper::field('is_active') . "\n\n";

// 檢查翻譯檔案
echo "檢查翻譯檔案:\n";
$translationFile = 'lang/zh_TW/filament-resources.php';
if (file_exists($translationFile)) {
    echo "翻譯檔案存在: {$translationFile}\n";
    $translations = include $translationFile;
    echo "fields.campus_name 存在: " . (isset($translations['fields']['campus_name']) ? '是' : '否') . "\n";
    echo "fields.address 存在: " . (isset($translations['fields']['address']) ? '是' : '否') . "\n";
    echo "fields.phone 存在: " . (isset($translations['fields']['phone']) ? '是' : '否') . "\n";
    echo "fields.email_address 存在: " . (isset($translations['fields']['email_address']) ? '是' : '否') . "\n";
    echo "fields.is_active 存在: " . (isset($translations['fields']['is_active']) ? '是' : '否') . "\n";
} else {
    echo "翻譯檔案不存在: {$translationFile}\n";
}
