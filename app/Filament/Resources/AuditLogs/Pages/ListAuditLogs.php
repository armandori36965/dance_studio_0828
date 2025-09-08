<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected static ?string $title = '審計日誌列表';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('新增'),
        ];
    }
}
