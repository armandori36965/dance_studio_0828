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
                    ->dateTime('Y-m-d H:i'), // 使用24小時制格式

                // 結束時間
                TextEntry::make('end_time')
                    ->label('結束時間')
                    ->dateTime('Y-m-d H:i'), // 使用24小時制格式

                // 事件類型
                TextEntry::make('category')
                    ->label('事件類型')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'national_holiday' => 'danger',
                        'periodic_assessment' => 'warning',
                        'disaster_drill' => 'info',
                        'school_anniversary' => 'success',
                        'todo' => 'primary',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'national_holiday' => __('fields.national_holiday'),
                        'periodic_assessment' => __('fields.periodic_assessment'),
                        'disaster_drill' => __('fields.disaster_drill'),
                        'school_anniversary' => __('fields.school_anniversary'),
                        'todo' => __('fields.todo'),
                        'other' => __('fields.other'),
                        default => $state,
                    }),

                // 所屬校區
                TextEntry::make('campus.name')
                    ->label('所屬校區')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) {
                            return $state;
                        }
                        // 如果是國定假日，顯示「國定假日」
                        return $record->category === 'national_holiday' ? '國定假日' : '未指定';
                    }),

                // 創建者
                TextEntry::make('creator.name')
                    ->label('創建者'),

                // 建立時間
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i'), // 使用24小時制格式

                // 更新時間
                TextEntry::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y-m-d H:i'), // 使用24小時制格式
            ]);
    }
}
