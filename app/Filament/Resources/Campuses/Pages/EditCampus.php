<?php

namespace App\Filament\Resources\Campuses\Pages;

use App\Filament\Resources\Campuses\CampusResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCampus extends EditRecord
{
    protected static string $resource = CampusResource::class;

    protected static ?string $title = '編輯校區';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('查看'),
            DeleteAction::make()
                ->label('刪除'),
        ];
    }

    // 移除業務功能標籤頁，只顯示編輯表單
    public function getRelationManagers(): array
    {
        return [];
    }
}
