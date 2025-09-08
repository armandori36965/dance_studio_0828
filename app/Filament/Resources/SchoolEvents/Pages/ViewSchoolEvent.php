<?php

namespace App\Filament\Resources\SchoolEvents\Pages;

use App\Filament\Resources\SchoolEvents\SchoolEventResource;
use App\Filament\Resources\Campuses\CampusResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewSchoolEvent extends ViewRecord
{
    protected static string $resource = SchoolEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 返回校區按鈕
            Action::make('back_to_campus')
                ->label('校區')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => $this->getRecord()->campus
                    ? CampusResource::getUrl('view', ['record' => $this->getRecord()->campus])
                    : CampusResource::getUrl('index')
                )
                ->openUrlInNewTab(false)
                ->visible(fn () => $this->getRecord()->campus !== null),

            EditAction::make(),
        ];
    }
}
