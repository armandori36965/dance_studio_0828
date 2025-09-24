<?php

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EquipmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(__('fields.equipment_name')),
                TextEntry::make('serial_number')
                    ->label(__('fields.serial_number')),
                TextEntry::make('status')
                    ->label(__('fields.status')),
                TextEntry::make('campus.name')
                    ->label(__('fields.campus_name')),
                TextEntry::make('purchase_date')
                    ->label(__('fields.purchase_date'))
                    ->date(),
                TextEntry::make('purchase_price')
                    ->label(__('fields.purchase_price'))
                    ->numeric(),
                TextEntry::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i'), // 使用24小時制格式
                TextEntry::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i'), // 使用24小時制格式
            ]);
    }
}
