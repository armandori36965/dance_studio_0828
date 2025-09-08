<?php

namespace App\Filament\Resources\SystemSettings\Pages;

use App\Filament\Resources\SystemSettings\SystemSettingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSystemSetting extends ViewRecord
{
    protected static string $resource = SystemSettingResource::class;

    protected static ?string $title = '查看系統設定';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('編輯'),
        ];
    }
}
