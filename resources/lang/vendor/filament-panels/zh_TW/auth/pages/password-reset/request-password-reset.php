<?php

return [

    'title' => '重設密碼',

    'heading' => '重設密碼',

    'description' => '請輸入您的電子郵件地址，我們將發送密碼重設連結給您。',

    'actions' => [

        'login' => [
            'before' => '或',
            'label' => '返回登入',
        ],

    ],

    'form' => [

        'email' => [
            'label' => '電子郵件地址',
        ],

        'actions' => [

            'request' => [
                'label' => '發送重設連結',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => '重設密碼嘗試次數過多',
            'body' => '請在 :seconds 秒後重試。',
        ],

        'sent' => [
            'title' => '重設連結已發送',
            'body' => '如果該電子郵件地址存在於我們的系統中，我們已發送密碼重設連結給您。',
        ],

    ],

];

