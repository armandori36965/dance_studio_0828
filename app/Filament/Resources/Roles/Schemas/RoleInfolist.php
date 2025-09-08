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
                    ->label('角色名稱'),

                // 角色描述
                TextEntry::make('description')
                    ->label('角色描述'),

                // 權限列表
                TextEntry::make('permissions')
                    ->label('權限列表')
                    ->listWithLineBreaks(),

                // 用戶數量
                TextEntry::make('users_count')
                    ->label('用戶數量')
                    ->counts('users'),

                // 建立時間
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime(),

                // 更新時間
                TextEntry::make('updated_at')
                    ->label('更新時間')
                    ->dateTime(),
            ]);
    }
}
