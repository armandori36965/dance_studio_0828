<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 基本資訊欄位
                TextInput::make('name')
                    ->label(__('fields.role_name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->columnSpan(1),

                Textarea::make('description')
                    ->label(__('fields.role_description'))
                    ->maxLength(500)
                    ->rows(3)
                    ->columnSpan(1),

                Toggle::make('is_active')
                    ->label(__('fields.is_active'))
                    ->default(true)
                    ->columnSpan(1),

                // 權限設定欄位
                CheckboxList::make('permissions')
                    ->label(__('fields.permissions'))
                    ->options([
                        // 校區管理權限
                        'campus.view' => __('fields.view') . __('fields.campuses'),
                        'campus.create' => __('fields.create') . __('fields.campuses'),
                        'campus.edit' => __('fields.edit') . __('fields.campuses'),
                        'campus.delete' => __('fields.delete') . __('fields.campuses'),

                        // 課程管理權限
                        'course.view' => __('fields.view') . __('fields.courses'),
                        'course.create' => __('fields.create') . __('fields.courses'),
                        'course.edit' => __('fields.edit') . __('fields.courses'),
                        'course.delete' => __('fields.delete') . __('fields.courses'),

                        // 學生管理權限
                        'student.view' => __('fields.view') . __('fields.users'),
                        'student.create' => __('fields.create') . __('fields.users'),
                        'student.edit' => __('fields.edit') . __('fields.users'),
                        'student.delete' => __('fields.delete') . __('fields.users'),

                        // 出勤管理權限
                        'attendance.view' => __('fields.view') . __('fields.attendances'),
                        'attendance.create' => __('fields.create') . __('fields.attendances'),
                        'attendance.edit' => __('fields.edit') . __('fields.attendances'),
                        'attendance.delete' => __('fields.delete') . __('fields.attendances'),

                        // 設備管理權限
                        'equipment.view' => __('fields.view') . __('fields.equipment'),
                        'equipment.create' => __('fields.create') . __('fields.equipment'),
                        'equipment.edit' => __('fields.edit') . __('fields.equipment'),
                        'equipment.delete' => __('fields.delete') . __('fields.equipment'),

                        // 財務管理權限
                        'finance.view' => __('fields.view') . __('fields.finances'),
                        'finance.create' => __('fields.create') . __('fields.finances'),
                        'finance.edit' => __('fields.edit') . __('fields.finances'),
                        'finance.delete' => __('fields.delete') . __('fields.finances'),
                        'finance.report' => __('fields.reports'),

                        // 行事曆管理權限
                        'view_calendar' => '查看行事曆',
                        'create_events' => '建立校務事件',
                        'update_events' => '更新校務事件',
                        'delete_events' => '刪除校務事件',
                        'create_courses' => '建立課程',
                        'update_courses' => '更新課程',
                        'delete_courses' => '刪除課程',
                        'manage_campus_events' => '管理校區事件',
                        'manage_campus_courses' => '管理校區課程',

                        // 系統管理權限
                        'user.manage' => __('fields.manage') . __('fields.users'),
                        'role.manage' => __('fields.manage') . __('fields.roles'),
                        'system.setting' => __('fields.settings'),
                        'audit.log' => __('filament-resources.pages.logs'),
                    ])
                    ->columns(3)
                    ->gridDirection('row')
                    ->searchable()
                    ->bulkToggleable()
                    ->columnSpanFull(),
            ]);
    }
}
