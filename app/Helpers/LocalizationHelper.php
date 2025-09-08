<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Lang;

class LocalizationHelper
{
    /**
     * 獲取欄位標籤翻譯
     */
    public static function field(string $key, array $parameters = []): string
    {
        return __('fields.' . $key, $parameters, 'zh_TW') ?? $key;
    }

    /**
     * 獲取操作按鈕翻譯
     */
    public static function action(string $key, array $parameters = []): string
    {
        return __('fields.' . $key, $parameters, 'zh_TW') ?? $key;
    }

    /**
     * 獲取狀態標籤翻譯
     */
    public static function status(string $key, array $parameters = []): string
    {
        return __('fields.' . $key, $parameters, 'zh_TW') ?? $key;
    }

    /**
     * 獲取頁面標題翻譯
     */
    public static function page(string $key, array $parameters = []): string
    {
        return __('fields.' . $key, $parameters, 'zh_TW') ?? $key;
    }

    /**
     * 獲取資源名稱翻譯
     */
    public static function resource(string $key, array $parameters = []): string
    {
        return __('fields.' . $key, $parameters, 'zh_TW') ?? $key;
    }

    /**
     * 獲取模組狀態翻譯
     */
    public static function moduleStatus(string $module, string $status, array $parameters = []): string
    {
        return Lang::get("fields.{$module}_{$status}", $parameters, 'zh_TW') ??
               Lang::get("fields.{$status}", $parameters, 'zh_TW') ??
               $status;
    }

    /**
     * 獲取模組狀態選項
     */
    public static function moduleStatusOptions(string $module): array
    {
        $options = [];

        // 通用狀態選項
        $commonStatuses = ['active', 'inactive', 'pending', 'completed', 'cancelled'];

        foreach ($commonStatuses as $status) {
            $key = "{$module}_{$status}";
            $options[$status] = __('fields.' . $key) ?? __('fields.' . $status) ?? $status;
        }

        return $options;
    }

    /**
     * 獲取頁面完整標題
     */
    public static function pageTitle(string $action, string $resource): string
    {
        $actionText = self::action($action);
        $resourceText = self::resource($resource);

        return "{$actionText}{$resourceText}";
    }
}
