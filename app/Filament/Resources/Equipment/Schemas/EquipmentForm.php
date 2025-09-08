<?php

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Lang;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Support\Carbon;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label(__('fields.equipment_name'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('fields.equipment_description'))
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('serial_number')
                    ->label(__('fields.serial_number'))
                    ->default(null),
                Select::make('status')
                    ->label(__('fields.status'))
                    ->options([
                        'available' => Lang::get('status.equipment.available', [], 'zh_TW'),
                        'in_use' => Lang::get('status.equipment.in_use', [], 'zh_TW'),
                        'maintenance' => Lang::get('status.equipment.maintenance', [], 'zh_TW'),
                        'broken' => Lang::get('status.equipment.broken', [], 'zh_TW'),
                    ])
                    ->default('available')
                    ->required(),
                Select::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->options(\App\Models\Campus::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                DateTimePicker::make('purchase_date')
                    ->label(__('fields.purchase_date'))
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->default(now()),
                TextInput::make('purchase_price')
                    ->label(__('fields.purchase_price'))
                    ->numeric()
                    ->prefix('NT$')
                    ->default(null),
                Textarea::make('maintenance_notes')
                    ->label(__('fields.maintenance_notes'))
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
