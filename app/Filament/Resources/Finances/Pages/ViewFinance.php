<?php

namespace App\Filament\Resources\Finances\Pages;

use App\Filament\Resources\Finances\FinanceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFinance extends ViewRecord
{
    protected static string $resource = FinanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
