<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use App\Models\Campus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // 用戶姓名欄位
                TextInput::make('name')
                    ->label(__('fields.user_name'))
                    ->required()
                    ->maxLength(255),

                // 電子郵件欄位
                TextInput::make('email')
                    ->label(__('fields.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                // 電子郵件驗證時間
                DateTimePicker::make('email_verified_at')
                    ->label(__('fields.email_verified_at'))
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false),

                // 密碼欄位
                TextInput::make('password')
                    ->label(__('fields.password'))
                    ->password()
                    ->minLength(8)
                    ->dehydrated(fn ($state) => filled($state)) // 只有當有值時才保存
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // 自動加密密碼
                    ->required(fn (string $context): bool => $context === 'create'), // 只在創建時必填

                // 角色選擇欄位
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
                    }),

                // 校區選擇欄位
                Select::make('campus_id')
                    ->label(__('fields.campus'))
                    ->options(function () {
                        return Campus::where('is_active', true)
                            ->orderBy('sort_order', 'asc')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('選擇校區')
                    ->visible(fn (callable $get, $record) =>
                        $get('campus_required') ||
                        ($record && self::roleRequiresCampus($record->role_id))
                    )
                    ->required(fn (callable $get, $record) =>
                        $get('campus_required') ||
                        ($record && self::roleRequiresCampus($record->role_id))
                    ),

                // 緊急聯絡人姓名欄位
                TextInput::make('emergency_contact_name')
                    ->label('緊急聯絡人姓名')
                    ->visible(fn (callable $get, $record) =>
                        self::isStudentRole($get('role_id')) ||
                        ($record && self::isStudentRole($record->role_id))
                    )
                    ->required(fn (callable $get, $record) =>
                        self::isStudentRole($get('role_id')) ||
                        ($record && self::isStudentRole($record->role_id))
                    ),

                // 緊急聯絡人電話欄位
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
                    ),
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
}
