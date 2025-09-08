<?php

namespace App\Filament\Resources\Campuses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CampusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('fields.campus_name'))
                    ->required(),
                TextInput::make('address')
                    ->label(__('fields.address'))
                    ->default(null),
                TextInput::make('phone')
                    ->label(__('fields.phone'))
                    ->tel()
                    ->default(null),
                TextInput::make('email')
                    ->label(__('fields.email_address'))
                    ->email()
                    ->default(null),
                Textarea::make('description')
                    ->label(__('fields.description'))
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('fields.is_active'))
                    ->required(),
            ]);
    }
}
