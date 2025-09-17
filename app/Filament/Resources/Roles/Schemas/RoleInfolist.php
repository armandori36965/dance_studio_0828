<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RoleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 角色名稱
                TextEntry::make('name')
                    ->label(__('fields.role_name')),

                // 角色描述
                TextEntry::make('description')
                    ->label(__('fields.role_description')),

                // 是否需要校區
                TextEntry::make('requires_campus')
                    ->label('需要校區')
                    ->formatStateUsing(fn ($state) => $state ? '是' : '否'),

                // 權限列表
                TextEntry::make('permissions')
                    ->label(__('fields.permissions'))
                    ->listWithLineBreaks(),

                // 用戶數量
                TextEntry::make('users_count')
                    ->label(__('fields.user_count'))
                    ->counts('users'),

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
