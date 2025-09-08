<?php

namespace App\Filament\Resources\Campuses\Pages;

use App\Filament\Resources\Campuses\CampusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampuses extends ListRecords
{
    protected static string $resource = CampusResource::class;

    protected static ?string $title = '校區列表';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('新增'),
        ];
    }

}
