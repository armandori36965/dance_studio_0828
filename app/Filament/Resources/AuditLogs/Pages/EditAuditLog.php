<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAuditLog extends EditRecord
{
    protected static string $resource = AuditLogResource::class;

    protected static ?string $title = '編輯審計記錄';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('查看'),
            DeleteAction::make()
                ->label('刪除'),
            ForceDeleteAction::make()
                ->label('強制刪除'),
            RestoreAction::make()
                ->label('還原'),
        ];
    }
}
