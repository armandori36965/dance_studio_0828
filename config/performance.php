<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 效能優化配置
    |--------------------------------------------------------------------------
    |
    | 此檔案包含系統效能優化的相關配置
    |
    */

    // 資料庫查詢優化
    'database' => [
        // 預載入關聯的預設設定
        'default_eager_loading' => [
            'users' => ['role', 'campus'],
            'campuses' => ['users', 'courses', 'schoolEvents'],
            'courses' => ['campus'],
            'school_events' => ['campus', 'creator'],
            'attendances' => ['user', 'course', 'campus'],
            'equipment' => ['campus'],
            'finances' => ['campus', 'course', 'user'],
        ],
    ],

    // Widget 快取設定
    'widgets' => [
        'global_events_calendar' => [
            'cache_ttl' => 300, // 5 分鐘
            'cache_key' => 'global_events_calendar',
        ],
        'campus_stats' => [
            'cache_ttl' => 600, // 10 分鐘
            'cache_key' => 'campus_stats',
        ],
        'showcase_stats' => [
            'cache_ttl' => 300, // 5 分鐘
            'cache_key' => 'showcase_stats',
        ],
    ],

    // 表格分頁設定
    'tables' => [
        'default_per_page' => 25,
        'per_page_options' => [10, 25, 50, 100],
    ],

    // 查詢限制
    'query_limits' => [
        'max_events_per_calendar' => 1000,
        'max_records_per_table' => 10000,
    ],
];

