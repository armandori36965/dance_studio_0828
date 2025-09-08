<?php

namespace App\Config;

class TableColumnConfig
{
    /**
     * 校務事件表格欄位配置
     * 超級管理者可以修改此配置來決定哪些欄位不可切換
     */
    public static function getSchoolEventColumns(): array
    {
        return [
            // 核心欄位 - 不可切換
            'core' => [
                'title' => [
                    'label' => '標題',
                    'toggleable' => false,
                    'required' => true,
                ],
                'start_time' => [
                    'label' => '開始時間',
                    'toggleable' => false,
                    'required' => true,
                ],
                'category' => [
                    'label' => '類型',
                    'toggleable' => false,
                    'required' => true,
                ],
                'status' => [
                    'label' => '狀態',
                    'toggleable' => false,
                    'required' => true,
                ],
            ],

            // 可選欄位 - 可切換
            'optional' => [
                'description' => [
                    'label' => '描述',
                    'toggleable' => true,
                    'default_hidden' => true,
                ],
                'end_time' => [
                    'label' => '結束時間',
                    'toggleable' => true,
                    'default_hidden' => true,
                ],
                'location' => [
                    'label' => '地點',
                    'toggleable' => true,
                    'default_hidden' => true,
                ],
                'creator.name' => [
                    'label' => '建立者',
                    'toggleable' => true,
                    'default_hidden' => true,
                ],
                'created_at' => [
                    'label' => '建立時間',
                    'toggleable' => true,
                    'default_hidden' => true,
                ],
                'updated_at' => [
                    'label' => '更新時間',
                    'toggleable' => true,
                    'default_hidden' => true,
                ],
            ],
        ];
    }

    /**
     * 檢查欄位是否可切換
     */
    public static function isColumnToggleable(string $columnName): bool
    {
        $config = self::getSchoolEventColumns();

        // 檢查核心欄位
        if (isset($config['core'][$columnName])) {
            return $config['core'][$columnName]['toggleable'];
        }

        // 檢查可選欄位
        if (isset($config['optional'][$columnName])) {
            return $config['optional'][$columnName]['toggleable'];
        }

        // 預設為可切換
        return true;
    }

    /**
     * 檢查欄位是否預設隱藏
     */
    public static function isColumnHiddenByDefault(string $columnName): bool
    {
        $config = self::getSchoolEventColumns();

        // 檢查可選欄位
        if (isset($config['optional'][$columnName])) {
            return $config['optional'][$columnName]['default_hidden'] ?? false;
        }

        // 預設為顯示
        return false;
    }

    /**
     * 獲取欄位標籤
     */
    public static function getColumnLabel(string $columnName): string
    {
        $config = self::getSchoolEventColumns();

        // 檢查核心欄位
        if (isset($config['core'][$columnName])) {
            return $config['core'][$columnName]['label'];
        }

        // 檢查可選欄位
        if (isset($config['optional'][$columnName])) {
            return $config['optional'][$columnName]['label'];
        }

        // 預設標籤
        return $columnName;
    }
}


