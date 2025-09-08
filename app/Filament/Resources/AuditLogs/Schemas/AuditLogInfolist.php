<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 用戶
                TextEntry::make('user.name')
                    ->label('用戶'),

                // 操作
                TextEntry::make('action')
                    ->label('操作')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'create' => '建立',
                        'update' => '更新',
                        'delete' => '刪除',
                        'login' => '登入',
                        'logout' => '登出',
                        default => $state,
                    }),

                // 模型類型
                TextEntry::make('model_type')
                    ->label('模型類型')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\\Models\\User' => '用戶',
                        'App\\Models\\Campus' => '校區',
                        'App\\Models\\Course' => '課程',
                        'App\\Models\\SchoolEvent' => '校務活動',
                        'App\\Models\\Equipment' => '設備',
                        'App\\Models\\Finance' => '財務',
                        default => $state,
                    }),

                // 模型ID
                TextEntry::make('model_id')
                    ->label('模型ID'),

                // 舊值
                TextEntry::make('old_values')
                    ->label('舊值')
                    ->listWithLineBreaks()
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '無'),

                // 新值
                TextEntry::make('new_values')
                    ->label('新值')
                    ->listWithLineBreaks()
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '無'),

                // IP地址
                TextEntry::make('ip_address')
                    ->label('IP地址'),

                // 用戶代理
                TextEntry::make('user_agent')
                    ->label('用戶代理'),

                // 建立時間
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i:s'),
            ]);
    }
}
