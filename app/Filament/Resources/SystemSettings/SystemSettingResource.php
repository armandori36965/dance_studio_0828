<?php

namespace App\Filament\Resources\SystemSettings;

use App\Filament\Resources\SystemSettings\Pages\CreateSystemSetting;
use App\Filament\Resources\SystemSettings\Pages\EditSystemSetting;
use App\Filament\Resources\SystemSettings\Pages\ListSystemSettings;
use App\Filament\Resources\SystemSettings\Pages\ViewSystemSetting;
use App\Filament\Resources\SystemSettings\Schemas\SystemSettingForm;
use App\Filament\Resources\SystemSettings\Schemas\SystemSettingInfolist;
use App\Filament\Resources\SystemSettings\Tables\SystemSettingsTable;
use App\Models\SystemSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    // 設定導航圖示
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    // 設定導航標籤
    protected static ?string $navigationLabel = '系統設定';

    // 設定模型標籤（單數）
    protected static ?string $modelLabel = '系統設定';

    // 設定模型標籤（複數）
    protected static ?string $pluralModelLabel = '系統設定';

    // 設定排序順序
    protected static ?int $navigationSort = 9;

    protected static ?string $recordTitleAttribute = 'key';

    public static function getNavigationLabel(): string
    {
        return __('fields.system_settings');
    }

    public static function getModelLabel(): string
    {
        return __('fields.system_setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fields.system_settings');
    }

    public static function form(Schema $schema): Schema
    {
        return SystemSettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SystemSettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemSettingsTable::configure($table);
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
            'index' => ListSystemSettings::route('/'),
            'create' => CreateSystemSetting::route('/create'),
            'view' => ViewSystemSetting::route('/{record}'),
            'edit' => EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
