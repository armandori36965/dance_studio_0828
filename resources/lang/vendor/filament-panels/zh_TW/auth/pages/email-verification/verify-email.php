<?php

return [

    'title' => '驗證電子郵件',

    'heading' => '驗證您的電子郵件地址',

    'description' => '在繼續之前，請檢查您的電子郵件以獲取驗證連結。',

    'actions' => [

        'resend' => [
            'label' => '重新發送驗證電子郵件',
        ],

        'logout' => [
            'label' => '登出',
        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => '重新發送嘗試次數過多',
            'body' => '請在 :seconds 秒後重試。',
        ],

        'sent' => [
            'title' => '驗證連結已發送',
            'body' => '我們已發送新的驗證連結到您的電子郵件地址。',
        ],

    ],

];

