<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    protected static ?string $title = '查看審計記錄';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('編輯'),
        ];
    }
}
