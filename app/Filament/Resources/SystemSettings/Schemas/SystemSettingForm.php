<?php

namespace App\Filament\Resources\SystemSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SystemSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 設定鍵名
                Select::make('key')
                    ->label(__('fields.setting_key'))
                    ->options([
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
                    ])
                    ->required()
                    ->searchable()
                    ->placeholder(__('actions.choose') . __('fields.setting_type')),

                // 設定值
                TextInput::make('value')
                    ->label(__('fields.setting_value'))
                    ->required()
                    ->maxLength(1000)
                    ->placeholder(__('actions.enter') . __('fields.setting_value'))
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // 根據設定類型動態調整欄位類型
                        $type = $get('type');
                        if ($type === 'email') {
                            $set('value', '');
                        } elseif ($type === 'url') {
                            $set('value', '');
                        } elseif ($type === 'boolean') {
                            $set('value', 'true');
                        }
                    }),

                // 設定描述
                Textarea::make('description')
                    ->label(__('fields.setting_description'))
                    ->maxLength(500)
                    ->rows(3),

                // 設定類型
                Select::make('type')
                    ->label(__('fields.setting_type'))
                    ->options([
                        'string' => __('fields.string_type'),
                        'number' => __('fields.number_type'),
                        'boolean' => __('fields.boolean_type'),
                        'json' => __('fields.json_type'),
                        'email' => __('fields.email_type'),
                        'url' => __('fields.url_type'),
                        'date' => __('fields.date_type'),
                        'time' => __('fields.time_type'),
                        'datetime' => __('fields.datetime_type'),
                    ])
                    ->default('string')
                    ->required()
                    ->placeholder(__('actions.choose') . __('fields.setting_type')),
            ]);
    }
}
