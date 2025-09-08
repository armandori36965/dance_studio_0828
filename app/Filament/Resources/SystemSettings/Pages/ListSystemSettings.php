<?php

namespace App\Filament\Resources\SystemSettings\Pages;

use App\Filament\Resources\SystemSettings\SystemSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSystemSettings extends ListRecords
{
    protected static string $resource = SystemSettingResource::class;

    protected static ?string $title = '系統設定列表';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('新增'),
        ];
    }
}
