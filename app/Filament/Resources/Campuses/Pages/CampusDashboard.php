<?php

namespace App\Filament\Resources\Campuses\Pages;

use App\Filament\Resources\Campuses\CampusResource;
use App\Filament\Resources\Campuses\Relations\CampusSchoolEvents;
use App\Filament\Resources\Campuses\Relations\CampusCourses;
use App\Filament\Resources\Campuses\Relations\CampusUsers;
use App\Filament\Resources\Campuses\Relations\CampusAttendances;
use App\Filament\Resources\Campuses\Relations\CampusEquipment;
use App\Filament\Resources\Campuses\Relations\CampusFinances;
use App\Filament\Widgets\CampusStatsWidget;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Actions\Action;

class CampusDashboard extends EditRecord
{
    protected static string $resource = CampusResource::class;

    protected static ?string $title = ' ';

    // 設定頁面標題
    public function getTitle(): string
    {
        return $this->getRecord()->name . ' ';
    }

    // 設定頁面描述
    public function getDescription(): string
    {
        return '校區統計概覽和業務管理';
    }

    // 設定頁面頭部 Widget（統計資訊）
    protected function getHeaderWidgets(): array
    {
        return [
            CampusStatsWidget::make([
                'campus' => $this->getRecord(),
            ]),
        ];
    }

    // 設定右上角操作按鈕
    protected function getHeaderActions(): array
    {
        return [
            // 詳情按鈕 - 導航到 ViewCampus
            Action::make('details')
                ->label('詳情')
                ->icon('heroicon-o-information-circle')
                ->url(fn () => CampusResource::getUrl('details', ['record' => $this->getRecord()]))
                ->openUrlInNewTab(false),
        ];
    }

    // 移除校區詳情顯示
    public function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    // 移除表單顯示
    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    // 隱藏保存和取消按鈕
    protected function getFormActions(): array
    {
        return [];
    }

    // 設定關聯管理器（業務功能標籤頁）
    public function getRelationManagers(): array
    {
        return [
            CampusSchoolEvents::class,
            CampusCourses::class,
            CampusUsers::class,
            CampusAttendances::class,
            CampusEquipment::class,
            CampusFinances::class,
        ];
    }
}
