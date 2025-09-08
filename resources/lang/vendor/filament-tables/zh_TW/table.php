<?php

return [
    'fields' => [
        'search' => [
            'placeholder' => '搜尋...',
            'label' => '搜尋',
            'indicator' => '搜尋',
        ],
        'bulk_select_page' => [
            'label' => '選擇頁面',
        ],
        'bulk_select_group' => [
            'label' => '選擇群組',
        ],
        'bulk_select_record' => [
            'label' => '選擇記錄',
        ],
    ],

    'empty' => [
        'heading' => '未找到資料',
        'description' => '沒有符合條件的記錄',
    ],

    'filters' => [
        'heading' => '篩選',
        'indicator' => '篩選器',
        'actions' => [
            'remove' => [
                'label' => '移除',
            ],
            'remove_all' => [
                'label' => '移除全部',
            ],
            'apply' => [
                'label' => '套用篩選',
            ],
            'reset' => [
                'label' => '重設篩選',
            ],
        ],
        'multi_select' => [
            'placeholder' => '全部',
        ],
        'select' => [
            'placeholder' => '全部',
            'relationship' => [
                'empty_option_label' => '選擇關聯',
            ],
        ],
        'trashed' => [
            'label' => '已刪除的資料',
            'only_trashed' => '僅顯示已刪除的資料',
            'with_trashed' => '包含已刪除的資料',
            'without_trashed' => '不含已刪除的資料',
        ],
    ],

    'column_manager' => [
        'heading' => '欄位管理',
        'actions' => [
            'reset' => [
                'label' => '重設',
            ],
            'apply' => [
                'label' => '套用',
            ],
        ],
    ],

    'actions' => [
        'filter' => [
            'label' => '篩選',
        ],
        'open_bulk_actions' => [
            'label' => '打開動作',
        ],
        'column_manager' => [
            'label' => '顯示／隱藏直列',
            'heading' => '欄位管理',
            'actions' => [
                'reset' => [
                    'label' => '重設',
                ],
                'apply' => [
                    'label' => '套用',
                ],
            ],
        ],
        'disable_reordering' => [
            'label' => '停用重新排序',
        ],
        'enable_reordering' => [
            'label' => '啟用重新排序',
        ],
        'group' => [
            'label' => '群組',
        ],
    ],

    'grouping' => [
        'fields' => [
            'label' => '群組欄位',
        ],
        'direction' => [
            'label' => '群組方向',
        ],
        'options' => [
            'asc' => '升序',
            'desc' => '降序',
        ],
    ],

    'sorting' => [
        'fields' => [
            'label' => '排序欄位',
        ],
        'direction' => [
            'label' => '排序方向',
        ],
        'options' => [
            'asc' => '升序',
            'desc' => '降序',
        ],
    ],

    'bulk_actions' => [
        'label' => '批量動作',
        'selected' => '已選擇 :count 筆記錄',
        'actions' => [
            'delete' => [
                'label' => '刪除',
                'modal' => [
                    'heading' => '確定要刪除選中的記錄嗎？',
                    'description' => '此操作無法撤銷。',
                    'actions' => [
                        'confirm' => [
                            'label' => '確定刪除',
                        ],
                        'cancel' => [
                            'label' => '取消',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'selection_indicator' => [
        'selected_count' => '已選擇 :count 個項目',
        'actions' => [
            'select_all' => [
                'label' => '選擇全部 :count 項',
            ],
            'deselect_all' => [
                'label' => '取消選擇全部',
            ],
        ],
    ],

    'reorder_indicator' => '拖拽記錄以重新排序。',
];
