<?php

namespace App\Filament\Resources\Campuses\Pages;

use App\Filament\Resources\Campuses\CampusResource;
use App\Filament\Resources\Campuses\Relations\CampusSchoolEvents;
use App\Filament\Resources\Campuses\Relations\CampusCourses;
use App\Filament\Resources\Campuses\Relations\CampusUsers;
use App\Filament\Resources\Campuses\Relations\CampusAttendances;
use App\Filament\Resources\Campuses\Relations\CampusEquipment;
use App\Filament\Resources\Campuses\Relations\CampusFinances;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewCampus extends ViewRecord
{
    protected static string $resource = CampusResource::class;

    protected static ?string $title = '查看校區';

    protected function getHeaderActions(): array
    {
        return [
            // 返回按鈕 - 導航到校區儀表板
            Action::make('back')
                ->label('返回')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => CampusResource::getUrl('view', ['record' => $this->getRecord()]))
                ->openUrlInNewTab(false),

            // 編輯按鈕
            EditAction::make()
                ->label('編輯'),
        ];
    }

    // 移除業務功能標籤頁，只顯示校區基本資訊
    public function getRelationManagers(): array
    {
        return [];
    }
}

