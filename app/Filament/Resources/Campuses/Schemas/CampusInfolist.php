<?php

namespace App\Filament\Resources\Campuses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Schemas\Schema;

class CampusInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('校區名稱'),
                TextEntry::make('address')
                    ->label('地址'),
                TextEntry::make('phone')
                    ->label('電話'),
                TextEntry::make('email')
                    ->label('電子郵件地址'),
                ColorEntry::make('color')
                    ->label('校區顏色')
                    ->default('#3B82F6'),
                IconEntry::make('is_active')
                    ->label('啟用狀態')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i:s'),
                TextEntry::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y-m-d H:i:s'),
            ]);
    }
}
