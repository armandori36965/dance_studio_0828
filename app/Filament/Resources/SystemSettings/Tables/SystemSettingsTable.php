<?php

namespace App\Filament\Resources\SystemSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SystemSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // 設定鍵名欄位
                TextColumn::make('key')
                    ->label(__('fields.setting_key'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
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

                // 設定值欄位
                TextColumn::make('value')
                    ->label(__('fields.setting_value'))
                    ->searchable()
                    ->limit(50)
                    ->formatStateUsing(function ($state, $record) {
                        $key = $record->key;
                        $options = [
                            'site_name' => __('fields.site_name'),
                            'site_description' => __('fields.site_description'),
                            'contact_email' => __('fields.contact_email'),
                            'contact_phone' => __('fields.contact_phone'),
                            'business_hours' => __('fields.business_hours'),
                            'address' => __('fields.address'),
                            'currency' => __('fields.currency'),
                            'timezone' => __('fields.timezone'),
                            'language' => __('fields.language'),
                            'maintenance_mode' => __('fields.maintenance_mode'),
                            'enable_registration' => __('fields.enable_registration'),
                            'enable_login' => __('fields.enable_login'),
                            'enable_password_reset' => __('fields.enable_password_reset'),
                            'enable_email_verification' => __('fields.enable_email_verification'),
                            'max_login_attempts' => __('fields.max_login_attempts'),
                            'session_timeout' => __('fields.session_timeout'),
                            'default_role' => __('fields.default_role'),
                            'default_permissions' => __('fields.default_permissions'),
                            'backup_enabled' => __('fields.backup_enabled'),
                            'backup_frequency' => __('fields.backup_frequency'),
                            'log_level' => __('fields.log_level'),
                            'debug_mode' => __('fields.debug_mode'),
                            'cache_enabled' => __('fields.cache_enabled'),
                            'mail_driver' => __('fields.mail_driver'),
                            'mail_host' => __('fields.mail_host'),
                            'mail_port' => __('fields.mail_port'),
                            'mail_username' => __('fields.mail_username'),
                            'mail_password' => __('fields.mail_password'),
                            'mail_encryption' => __('fields.mail_encryption'),
                            'mail_from_address' => __('fields.mail_from_address'),
                            'mail_from_name' => __('fields.mail_from_name'),
                            'max_students_per_course' => __('fields.max_students_per_course'),
                            'min_students_per_course' => __('fields.min_students_per_course'),
                            'course_duration' => __('fields.course_duration'),
                            'break_time' => __('fields.break_time'),
                            'classroom_capacity' => __('fields.classroom_capacity'),
                            'equipment_rental_fee' => __('fields.equipment_rental_fee'),
                            'late_fee' => __('fields.late_fee'),
                            'absence_fee' => __('fields.absence_fee'),
                            'refund_policy' => __('fields.refund_policy'),
                            'cancellation_policy' => __('fields.cancellation_policy'),
                            'holiday_schedule' => __('fields.holiday_schedule'),
                            'exam_schedule' => __('fields.exam_schedule'),
                            'performance_schedule' => __('fields.performance_schedule'),
                            'competition_schedule' => __('fields.competition_schedule'),
                            'workshop_schedule' => __('fields.workshop_schedule'),
                            'master_class_schedule' => __('fields.master_class_schedule'),
                        ];

                        if (in_array($key, ['maintenance_mode', 'enable_registration', 'enable_login', 'enable_password_reset', 'enable_email_verification'])) {
                            return $state === 'true' ? __('status.active') : __('status.inactive');
                        }

                        return $options[$key] ?? $state;
                    }),

                // 設定描述欄位
                TextColumn::make('description')
                    ->label('設定描述')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: false),

                // 設定類型欄位
                TextColumn::make('type')
                    ->label('設定類型')
                    ->badge()
                    ->color('info')
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

                // 建立時間欄位
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 更新時間欄位
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 設定類型過濾器
                SelectFilter::make('type')
                    ->label('設定類型')
                    ->options([
                        'string' => '字串',
                        'number' => '數字',
                        'boolean' => '布林值',
                        'json' => 'JSON',
                        'email' => '電子郵件',
                        'url' => '網址',
                        'date' => '日期',
                        'time' => '時間',
                        'datetime' => '日期時間',
                    ]),
            ])
            ->recordActions([
                // 記錄操作按鈕
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                // 工具列操作按鈕
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->extremePaginationLinks() // 顯示第一頁和最後一頁按鈕
            ->paginated([10, 25, 50, 100]) // 設定每頁顯示筆數選項
            ->defaultPaginationPageOption(10) // 預設每頁顯示10筆
            ->paginationPageOptions([10, 25, 50, 100]) // 確保分頁選項正確設定
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
