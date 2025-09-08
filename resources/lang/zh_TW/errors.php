<?php

return [
    'general' => [
        'error' => '發生錯誤',
        'success' => '操作成功',
        'warning' => '警告',
        'info' => '資訊',
        'unknown_error' => '發生未知錯誤',
        'try_again' => '請稍後再試',
        'contact_support' => '如有問題，請聯繫技術支援',
    ],

    'validation' => [
        'required' => ':attribute 是必填欄位',
        'email' => ':attribute 必須是有效的電子郵件地址',
        'unique' => ':attribute 已經存在',
        'min' => [
            'string' => ':attribute 至少需要 :min 個字元',
            'numeric' => ':attribute 至少需要 :min',
        ],
        'max' => [
            'string' => ':attribute 不能超過 :min 個字元',
            'numeric' => ':attribute 不能超過 :max',
        ],
        'between' => [
            'string' => ':attribute 長度必須在 :min 到 :max 個字元之間',
            'numeric' => ':attribute 必須在 :min 到 :max 之間',
        ],
        'confirmed' => ':attribute 確認不匹配',
        'different' => ':attribute 和 :other 必須不同',
        'digits' => ':attribute 必須是 :digits 位數字',
        'digits_between' => ':attribute 必須是 :min 到 :max 位數字',
        'exists' => '選擇的 :attribute 無效',
        'file' => ':attribute 必須是一個檔案',
        'image' => ':attribute 必須是一個圖片',
        'in' => '選擇的 :attribute 無效',
        'integer' => ':attribute 必須是一個整數',
        'ip' => ':attribute 必須是一個有效的 IP 地址',
        'json' => ':attribute 必須是一個有效的 JSON 字串',
        'mimes' => ':attribute 必須是一個 :values 類型的檔案',
        'mimetypes' => ':attribute 必須是一個 :values 類型的檔案',
        'not_in' => '選擇的 :attribute 無效',
        'numeric' => ':attribute 必須是一個數字',
        'present' => ':attribute 欄位必須存在',
        'regex' => ':attribute 格式無效',
        'required_if' => '當 :other 為 :value 時，:attribute 是必填欄位',
        'required_unless' => '除非 :other 在 :values 中，否則 :attribute 是必填欄位',
        'required_with' => '當 :values 存在時，:attribute 是必填欄位',
        'required_with_all' => '當 :values 都存在時，:attribute 是必填欄位',
        'required_without' => '當 :values 不存在時，:attribute 是必填欄位',
        'required_without_all' => '當 :values 都不存在時，:attribute 是必填欄位',
        'same' => ':attribute 和 :other 必須匹配',
        'size' => [
            'string' => ':attribute 必須是 :size 個字元',
            'numeric' => ':attribute 必須是 :size',
            'file' => ':attribute 必須是 :size KB',
            'image' => ':attribute 必須是 :size KB',
        ],
        'string' => ':attribute 必須是一個字串',
        'timezone' => ':attribute 必須是一個有效的時區',
        'url' => ':attribute 格式無效',
    ],

    'database' => [
        'connection_failed' => '資料庫連接失敗',
        'query_failed' => '資料庫查詢失敗',
        'transaction_failed' => '資料庫交易失敗',
        'record_not_found' => '找不到指定的記錄',
        'duplicate_entry' => '記錄已存在',
        'constraint_violation' => '資料完整性約束違反',
    ],

    'file' => [
        'upload_failed' => '檔案上傳失敗',
        'file_too_large' => '檔案太大',
        'invalid_file_type' => '無效的檔案類型',
        'file_not_found' => '找不到指定的檔案',
        'permission_denied' => '沒有權限存取檔案',
    ],

    'auth' => [
        'failed' => '這些憑證與我們的記錄不符',
        'password' => '密碼錯誤',
        'throttle' => '登入嘗試次數過多，請在 :seconds 秒後再試',
        'unauthorized' => '未授權的存取',
        'forbidden' => '禁止存取',
        'not_found' => '找不到請求的資源',
    ],

    'business' => [
        'insufficient_permissions' => '權限不足',
        'resource_in_use' => '資源正在使用中',
        'operation_not_allowed' => '不允許此操作',
        'data_integrity_error' => '資料完整性錯誤',
        'business_rule_violation' => '違反業務規則',
    ],
];

