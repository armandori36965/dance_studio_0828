<?php

namespace App\Filament\Resources\Campuses;

use App\Filament\Resources\Campuses\Pages\CreateCampus;
use App\Filament\Resources\Campuses\Pages\EditCampus;
use App\Filament\Resources\Campuses\Pages\ListCampuses;
use App\Filament\Resources\Campuses\Pages\CampusDashboard;
use App\Filament\Resources\Campuses\Pages\ViewCampus;
use App\Filament\Resources\Campuses\Schemas\CampusForm;
use App\Filament\Resources\Campuses\Schemas\CampusInfolist;
use App\Filament\Resources\Campuses\Tables\CampusesTable;
use App\Models\Campus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CampusResource extends Resource
{
    protected static ?string $model = Campus::class;

    // 設定導航圖示
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    // 設定導航標籤
    protected static ?string $navigationLabel = '校區管理';

    // 設定模型標籤（單數）
    protected static ?string $modelLabel = '校區';

    // 設定模型標籤（複數）
    protected static ?string $pluralModelLabel = '校區';

    // 設定排序順序
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('fields.campuses');
    }

    public static function getModelLabel(): string
    {
        return __('fields.campus');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fields.campuses');
    }

    public static function form(Schema $schema): Schema
    {
        return CampusForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CampusInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampusesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // 校務事件管理
            \App\Filament\Resources\Campuses\Relations\CampusSchoolEvents::class,

            // 課程管理
            \App\Filament\Resources\Campuses\Relations\CampusCourses::class,

            // 人員管理
            \App\Filament\Resources\Campuses\Relations\CampusUsers::class,

            // 差勤管理
            \App\Filament\Resources\Campuses\Relations\CampusAttendances::class,

            // 設備管理
            \App\Filament\Resources\Campuses\Relations\CampusEquipment::class,

            // 財務管理
            \App\Filament\Resources\Campuses\Relations\CampusFinances::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampuses::route('/'),
            'create' => CreateCampus::route('/create'),
            'view' => CampusDashboard::route('/{record}'),
            'details' => ViewCampus::route('/{record}/details'),
            'edit' => EditCampus::route('/{record}/edit'),
        ];
    }

    // 權限控制方法
    public static function canEdit($record): bool
    {
        return true; // 暫時允許所有用戶編輯，可根據需要調整
    }

    public static function canDelete($record): bool
    {
        return true; // 暫時允許所有用戶刪除，可根據需要調整
    }
}
