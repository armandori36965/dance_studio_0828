<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
                    ->format('Y-m-d H:i:s'),

                // 密碼欄位
                TextInput::make('password')
                    ->label(__('fields.password'))
                    ->password()
                    ->required()
                    ->minLength(8),

                // 角色選擇欄位
                Select::make('role_id')
                    ->label(__('fields.role'))
                    ->options(Role::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('選擇用戶角色'),
            ]);
    }
}
