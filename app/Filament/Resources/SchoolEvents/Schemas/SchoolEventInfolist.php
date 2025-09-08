<?php

namespace App\Filament\Resources\SchoolEvents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SchoolEventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 校務事件標題
                TextEntry::make('title')
                    ->label('校務事件標題'),

                // 校務事件描述
                TextEntry::make('description')
                    ->label('校務事件描述')
                    ->columnSpanFull(),

                // 開始時間
                TextEntry::make('start_time')
                    ->label('開始時間')
                    ->dateTime(),

                // 結束時間
                TextEntry::make('end_time')
                    ->label('結束時間')
                    ->dateTime(),

                // 地點
                TextEntry::make('location')
                    ->label('地點'),

                // 事件類型
                TextEntry::make('category')
                    ->label('事件類型'),

                // 狀態
                TextEntry::make('status')
                    ->label('狀態')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'todo' => 'warning',
                        'pending' => 'warning', // 向後相容舊資料
                        'completed' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '啟用',
                        'inactive' => '停用',
                        'todo' => '待辦',
                        'pending' => '待辦', // 向後相容舊資料
                        'completed' => '完成',
                        default => $state,
                    }),

                // 所屬校區
                TextEntry::make('campus.name')
                    ->label('所屬校區'),

                // 創建者
                TextEntry::make('creator.name')
                    ->label('創建者'),

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
