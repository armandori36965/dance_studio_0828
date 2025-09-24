<?php

namespace App\Filament\Resources\SchoolEvents;

use App\Filament\Resources\SchoolEvents\Pages\CreateSchoolEvent;
use App\Filament\Resources\SchoolEvents\Pages\EditSchoolEvent;
use App\Filament\Resources\SchoolEvents\Pages\ListSchoolEvents;
use App\Filament\Resources\SchoolEvents\Pages\ViewSchoolEvent;
use App\Filament\Resources\SchoolEvents\Schemas\SchoolEventForm;
use App\Filament\Resources\SchoolEvents\Schemas\SchoolEventInfolist;
use App\Filament\Resources\SchoolEvents\Tables\SchoolEventsTable;
use App\Models\SchoolEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SchoolEventResource extends Resource
{
    protected static ?string $model = SchoolEvent::class;

    // 設定導航圖示
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    // 設定導航標籤
    protected static ?string $navigationLabel = '事件';

    // 設定模型標籤（單數）
    protected static ?string $modelLabel = '事件';

    // 設定模型標籤（複數）
    protected static ?string $pluralModelLabel = '事件';

    // 設定排序順序
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('fields.school_events');
    }

    public static function getModelLabel(): string
    {
        return __('fields.school_event');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fields.school_events');
    }

    public static function form(Schema $schema): Schema
    {
        return SchoolEventForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SchoolEventInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchoolEvents::route('/'),
            'create' => CreateSchoolEvent::route('/create'),
            'view' => ViewSchoolEvent::route('/{record}'),
            'edit' => EditSchoolEvent::route('/{record}/edit'),
        ];
    }
}
