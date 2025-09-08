<?php

namespace App\Filament\Resources\SystemSettings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SystemSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 設定鍵名
                TextEntry::make('key')
                    ->label('設定鍵名')
                    ->formatStateUsing(function ($state) {
                        // 格式化設定鍵名顯示
                        return match ($state) {
                            'site_name' => '網站名稱',
                            'site_description' => '網站描述',
                            'contact_email' => '聯絡信箱',
                            'contact_phone' => '聯絡電話',
                            'business_hours' => '營業時間',
                            'address' => '地址',
                            'currency' => '貨幣',
                            'timezone' => '時區',
                            'language' => '語言',
                            'maintenance_mode' => '維護模式',
                            'enable_registration' => '啟用註冊',
                            'enable_login' => '啟用登入',
                            'enable_password_reset' => '啟用密碼重設',
                            'enable_email_verification' => '啟用郵件驗證',
                            'max_login_attempts' => '最大登入嘗試次數',
                            'session_timeout' => '會話超時時間',
                            'default_role' => '預設角色',
                            'default_permissions' => '預設權限',
                            'backup_enabled' => '啟用備份',
                            'backup_frequency' => '備份頻率',
                            'log_level' => '日誌等級',
                            'debug_mode' => '除錯模式',
                            'cache_enabled' => '啟用快取',
                            'mail_driver' => '郵件驅動',
                            'mail_host' => '郵件主機',
                            'mail_port' => '郵件埠號',
                            'mail_username' => '郵件用戶名',
                            'mail_password' => '郵件密碼',
                            'mail_encryption' => '郵件加密',
                            'mail_from_address' => '郵件寄件人地址',
                            'mail_from_name' => '郵件寄件人姓名',
                            'max_students_per_course' => '每課程最大學生數',
                            'min_students_per_course' => '每課程最小學生數',
                            'course_duration' => '課程時長',
                            'break_time' => '休息時間',
                            'classroom_capacity' => '教室容量',
                            'equipment_rental_fee' => '設備租借費用',
                            'late_fee' => '遲到罰款',
                            'absence_fee' => '缺課罰款',
                            'refund_policy' => '退費政策',
                            'cancellation_policy' => '取消政策',
                            'holiday_schedule' => '假日安排',
                            'exam_schedule' => '考試安排',
                            'performance_schedule' => '表演安排',
                            'competition_schedule' => '比賽安排',
                            'workshop_schedule' => '工作坊安排',
                            'master_class_schedule' => '大師班安排',
                            default => $state,
                        };
                    }),

                // 設定值
                TextEntry::make('value')
                    ->label('設定值')
                    ->formatStateUsing(function ($state, $record) {
                        // 根據設定鍵名格式化顯示
                        return match ($record->key) {
                            'site_name' => $state,
                            'site_description' => $state,
                            'contact_email' => $state,
                            'contact_phone' => $state,
                            'business_hours' => $state,
                            'address' => $state,
                            'currency' => $state,
                            'timezone' => $state,
                            'language' => $state,
                            'maintenance_mode' => $state === 'true' ? '啟用' : '停用',
                            'enable_registration' => $state === 'true' ? '啟用' : '停用',
                            'enable_login' => $state === 'true' ? '啟用' : '停用',
                            'enable_password_reset' => $state === 'true' ? '啟用' : '停用',
                            'enable_email_verification' => $state === 'true' ? '啟用' : '停用',
                            'max_login_attempts' => $state,
                            'session_timeout' => $state,
                            'default_role' => $state,
                            'default_permissions' => $state,
                            'backup_enabled' => $state === 'true' ? '啟用' : '停用',
                            'backup_frequency' => $state,
                            'log_level' => $state,
                            'debug_mode' => $state === 'true' ? '啟用' : '停用',
                            'cache_enabled' => $state === 'true' ? '啟用' : '停用',
                            'mail_driver' => $state,
                            'mail_host' => $state,
                            'mail_port' => $state,
                            'mail_username' => $state,
                            'mail_password' => '***',
                            'mail_encryption' => $state,
                            'mail_from_address' => $state,
                            'mail_from_name' => $state,
                            'max_students_per_course' => $state,
                            'min_students_per_course' => $state,
                            'course_duration' => $state,
                            'break_time' => $state,
                            'classroom_capacity' => $state,
                            'equipment_rental_fee' => $state,
                            'late_fee' => $state,
                            'absence_fee' => $state,
                            'refund_policy' => $state,
                            'cancellation_policy' => $state,
                            'holiday_schedule' => $state,
                            'exam_schedule' => $state,
                            'performance_schedule' => $state,
                            'competition_schedule' => $state,
                            'workshop_schedule' => $state,
                            'master_class_schedule' => $state,
                            default => $state,
                        };
                    }),

                // 設定描述
                TextEntry::make('description')
                    ->label('設定描述'),

                // 設定類型
                TextEntry::make('type')
                    ->label('設定類型')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'string' => '字串',
                        'number' => '數字',
                        'boolean' => '布林值',
                        'json' => 'JSON',
                        'email' => '電子郵件',
                        'url' => '網址',
                        'date' => '日期',
                        'time' => '時間',
                        'datetime' => '日期時間',
                        default => $state,
                    }),

                // 建立時間
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i:s'),

                // 更新時間
                TextEntry::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y-m-d H:i:s'),
            ]);
    }
}
