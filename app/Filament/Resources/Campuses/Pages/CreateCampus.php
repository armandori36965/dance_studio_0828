<?php

namespace App\Filament\Resources\Campuses\Pages;

use App\Filament\Resources\Campuses\CampusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCampus extends CreateRecord
{
    protected static string $resource = CampusResource::class;

    protected static ?string $title = '新增校區';
}
