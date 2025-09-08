<?php

namespace App\Filament\Resources\SystemSettings\Pages;

use App\Filament\Resources\SystemSettings\SystemSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSystemSetting extends EditRecord
{
    protected static string $resource = SystemSettingResource::class;

    protected static ?string $title = '編輯系統設定';

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
