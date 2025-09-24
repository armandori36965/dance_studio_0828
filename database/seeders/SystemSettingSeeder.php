<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * 執行資料填充
     */
    public function run(): void
    {
        // 系統基本設定（如果不存在則建立）
        $settings = [
            [
                'key' => 'site_name',
                'value' => '舞蹈工作室管理系統',
                'description' => '網站名稱',
                'type' => 'string',
            ],
            [
                'key' => 'site_description',
                'value' => '專業的舞蹈工作室管理平台',
                'description' => '網站描述',
                'type' => 'string',
            ],
            [
                'key' => 'contact_email',
                'value' => 'admin@dance.com',
                'description' => '聯絡電子郵件',
                'type' => 'string',
            ],
            [
                'key' => 'contact_phone',
                'value' => '+886-2-1234-5678',
                'description' => '聯絡電話',
                'type' => 'string',
            ],
            [
                'key' => 'max_students_per_course',
                'value' => '20',
                'description' => '每門課程最大學生數',
                'type' => 'number',
            ],
            [
                'key' => 'enable_registration',
                'value' => 'true',
                'description' => '是否啟用學生註冊',
                'type' => 'boolean',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('系統設定已建立完成！');
    }
}
