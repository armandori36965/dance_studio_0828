<?php

namespace App\Filament\Resources\Finances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FinanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('type'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('campus_id')
                    ->numeric(),
                TextEntry::make('course_id')
                    ->numeric(),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('transaction_date')
                    ->date(),
                TextEntry::make('payment_method'),
                TextEntry::make('reference_number'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
