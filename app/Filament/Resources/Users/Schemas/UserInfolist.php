<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 用戶姓名
                TextEntry::make('name')
                    ->label(__('fields.user_name')),

                // 電子郵件
                TextEntry::make('email')
                    ->label(__('fields.email')),

                // 角色
                TextEntry::make('role.name')
                    ->label(__('fields.role')),

                // 電子郵件驗證時間
                TextEntry::make('email_verified_at')
                    ->label(__('fields.email_verified_at'))
                    ->dateTime(),

                // 建立時間
                TextEntry::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime(),

                // 更新時間
                TextEntry::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime(),
            ]);
    }
}
