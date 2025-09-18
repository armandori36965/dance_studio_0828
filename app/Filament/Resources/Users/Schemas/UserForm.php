<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use App\Models\Campus;
use App\Models\Course;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // 第一列：用戶姓名* 密碼*
                TextInput::make('name')
                    ->label(__('fields.user_name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),
                TextInput::make('password')
                    ->label(__('fields.password'))
                    ->password()
                    ->minLength(8)
                    ->dehydrated(fn ($state) => filled($state)) // 只有當有值時才保存
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // 自動加密密碼
                    ->required(fn (string $context): bool => $context === 'create') // 只在創建時必填
                    ->columnSpan(1),

                // 第二列：電子郵件* 角色
                TextInput::make('email')
                    ->label(__('fields.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpan(1),
                Select::make('role_id')
                    ->label(__('fields.role'))
                    ->options(function () {
                        return Role::orderBy('sort_order', 'asc')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('選擇用戶角色')
                    ->live() // 啟用即時更新
                    ->afterStateUpdated(function ($state, callable $set) {
                        // 根據角色決定是否需要校區
                        $requiresCampus = self::roleRequiresCampus($state);
                        $set('campus_required', $requiresCampus);
                    })
                    ->columnSpan(1),

                // 第三列：緊急聯絡人姓名*(選學生時) 緊急聯絡人電話*(選學生時)
                TextInput::make('emergency_contact_name')
                    ->label('緊急聯絡人姓名')
                    ->visible(fn (callable $get, $record) =>
                        self::isStudentRole($get('role_id')) ||
                        ($record && self::isStudentRole($record->role_id))
                    )
                    ->required(fn (callable $get, $record) =>
                        self::isStudentRole($get('role_id')) ||
                        ($record && self::isStudentRole($record->role_id))
                    )
                    ->columnSpan(1),
                TextInput::make('emergency_contact_phone')
                    ->label('緊急聯絡人電話')
                    ->tel()
                    ->visible(fn (callable $get, $record) =>
                        self::isStudentRole($get('role_id')) ||
                        ($record && self::isStudentRole($record->role_id))
                    )
                    ->required(fn (callable $get, $record) =>
                        self::isStudentRole($get('role_id')) ||
                        ($record && self::isStudentRole($record->role_id))
                    )
                    ->columnSpan(1),

                // 第四列：校區 班級(新增)(選學生時)
                Select::make('campus_id')
                    ->label('校區')
                    ->options(function () {
                        return Campus::where('is_active', true)
                            ->where('type', 'school')
                            ->orderBy('sort_order', 'asc')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('選擇校區')
                    ->live()
                    ->visible(fn (callable $get, $record) =>
                        self::needsCampusSelection($get('role_id')) ||
                        ($record && self::needsCampusSelection($record->role_id))
                    )
                    ->afterStateUpdated(function ($state, callable $set) {
                        // 清空課程選擇當校區改變時
                        $set('course_ids', []);
                    })
                    ->columnSpan(1),
                TextInput::make('class')
                    ->label('班級')
                    ->placeholder('例如：一年甲班、二年乙班')
                    ->visible(fn (callable $get, $record) =>
                        self::isStudentRole($get('role_id')) ||
                        ($record && self::isStudentRole($record->role_id))
                    )
                    ->columnSpan(1),

                // 第五列：該校區的課程(選擇後出現可複選)
                CheckboxList::make('course_ids')
                    ->label('該校區報名的課程')
                    ->options(function (callable $get) {
                        $campusId = $get('campus_id');

                        if ($campusId) {
                            // 顯示校區的課程
                            return Course::where('campus_id', $campusId)
                                ->where('is_active', true)
                                ->orderBy('name', 'asc')
                                ->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->visible(fn (callable $get, $record) =>
                        (self::isStudentRole($get('role_id')) && $get('campus_id')) ||
                        ($record && self::isStudentRole($record->role_id) && $record->campus_id)
                    )
                    ->columns(2)
                    ->gridDirection('row')
                    ->columnSpanFull(),

                // 第六列：補習班
                Select::make('cram_school_id')
                    ->label('補習班')
                    ->options(function () {
                        return Campus::where('is_active', true)
                            ->where('type', 'cram_school')
                            ->orderBy('sort_order', 'asc')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('選擇補習班')
                    ->live()
                    ->visible(fn (callable $get, $record) =>
                        self::needsCampusSelection($get('role_id')) ||
                        ($record && self::needsCampusSelection($record->role_id))
                    )
                    ->afterStateUpdated(function ($state, callable $set) {
                        // 清空課程選擇當補習班改變時
                        $set('cram_school_course_ids', []);
                    })
                    ->columnSpan(1),

                // 第七列：該補習班的課程(選擇後出現可複選)
                CheckboxList::make('cram_school_course_ids')
                    ->label('該補習班報名的課程')
                    ->options(function (callable $get) {
                        $cramSchoolId = $get('cram_school_id');

                        if ($cramSchoolId) {
                            // 顯示補習班的課程
                            return Course::where('campus_id', $cramSchoolId)
                                ->where('is_active', true)
                                ->orderBy('name', 'asc')
                                ->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->visible(fn (callable $get, $record) =>
                        (self::isStudentRole($get('role_id')) && $get('cram_school_id')) ||
                        ($record && self::isStudentRole($record->role_id) && $record->cram_school_id)
                    )
                    ->columns(2)
                    ->gridDirection('row')
                    ->columnSpanFull(),
            ]);
    }

    /**
     * 判斷角色是否需要校區
     */
    private static function roleRequiresCampus(?int $roleId): bool
    {
        if (!$roleId) {
            return false;
        }

        $role = Role::find($roleId);
        if (!$role) {
            return false;
        }

        // 直接使用資料庫中的 requires_campus 設定
        return $role->requires_campus ?? false;
    }

    /**
     * 判斷是否為學生角色
     */
    private static function isStudentRole(?int $roleId): bool
    {
        if (!$roleId) {
            return false;
        }

        $role = Role::find($roleId);
        if (!$role) {
            return false;
        }

        return $role->name === '學生';
    }

    /**
     * 判斷是否為校務窗口角色
     */
    private static function isStaffRole(?int $roleId): bool
    {
        if (!$roleId) {
            return false;
        }

        $role = Role::find($roleId);
        if (!$role) {
            return false;
        }

        return $role->name === '校務窗口';
    }

    /**
     * 判斷是否需要顯示校區和補習班選擇（學生或校務窗口）
     */
    private static function needsCampusSelection(?int $roleId): bool
    {
        return self::isStudentRole($roleId) || self::isStaffRole($roleId);
    }
}
