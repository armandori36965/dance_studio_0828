<?php

return [

    'title' => '重設密碼',

    'heading' => '重設密碼',

    'form' => [

        'email' => [
            'label' => '電子郵件地址',
        ],

        'password' => [
            'label' => '新密碼',
            'validation_attribute' => '新密碼',
        ],

        'password_confirmation' => [
            'label' => '確認新密碼',
        ],

        'actions' => [

            'reset' => [
                'label' => '重設密碼',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => '重設密碼嘗試次數過多',
            'body' => '請在 :seconds 秒後重試。',
        ],

        'reset' => [
            'title' => '密碼已重設',
            'body' => '您的密碼已成功重設。',
        ],

    ],

];

