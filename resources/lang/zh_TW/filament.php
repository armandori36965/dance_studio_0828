<?php

return [

    'tables' => [

        'empty' => [
            'description' => '目前沒有記錄。',
            'heading' => '沒有找到記錄',
        ],

        'columns' => [

            'text' => [

                'actions' => [
                    'collapse' => '收起',
                    'expand' => '展開',
                ],

            ],

            'icon' => [
                'label' => '圖示',
            ],

            'boolean' => [
                'true' => '是',
                'false' => '否',
            ],

            'date' => [
                'label' => '日期',
            ],

            'datetime' => [
                'label' => '日期時間',
            ],

            'time' => [
                'label' => '時間',
            ],

            'select' => [
                'label' => '選擇',
            ],

            'badge' => [
                'label' => '標籤',
            ],

        ],

        'filters' => [

            'actions' => [
                'apply' => '套用',
                'reset' => '重設',
            ],

            'indicator' => '已套用 :count 個過濾器',

        ],

        'actions' => [

            'modal' => [
                'heading' => '確認操作',
                'description' => '您確定要執行此操作嗎？此操作無法撤銷。',
                'actions' => [
                    'confirm' => '確認',
                    'cancel' => '取消',
                ],
            ],

        ],

        'bulk_actions' => [

            'select_all' => [
                'label' => '全選',
            ],

            'deselect_all' => [
                'label' => '取消全選',
            ],

        ],

        'pagination' => [
            'label' => '分頁導航',
            'overview' => '顯示第 :first 到第 :last 筆，共 :total 筆記錄',
            'previous' => '上一頁',
            'next' => '下一頁',
            'first' => '第一頁',
            'last' => '最後一頁',
        ],

        'search' => [
            'label' => '搜尋',
            'placeholder' => '搜尋記錄...',
        ],

        'reorder' => [
            'label' => '重新排序',
        ],

    ],

    'panels' => [

        'empty' => [
            'description' => '目前沒有記錄。',
            'heading' => '沒有找到記錄',
        ],

    ],

    'components' => [

        'table' => [

            'empty' => [
                'description' => '目前沒有記錄。',
                'heading' => '沒有找到記錄',
            ],

        ],

        'form' => [

            'actions' => [
                'save' => '儲存',
                'cancel' => '取消',
                'submit' => '提交',
                'reset' => '重設',
            ],

        ],

        'modal' => [
            'close' => '關閉',
        ],

    ],

    'resources' => [

        'pages' => [
            'create' => '新增 :label',
            'edit' => '編輯 :label',
            'view' => '查看 :label',
            'list' => ':label 列表',
        ],

    ],

    'actions' => [

        'create' => [
            'label' => '新增',
        ],

        'edit' => [
            'label' => '編輯',
        ],

        'delete' => [
            'label' => '刪除',
        ],

        'view' => [
            'label' => '查看',
        ],

        'save' => [
            'label' => '儲存',
        ],

        'cancel' => [
            'label' => '取消',
        ],

    ],

    'notifications' => [

        'success' => '成功',
        'error' => '錯誤',
        'warning' => '警告',
        'info' => '資訊',

    ],



];
