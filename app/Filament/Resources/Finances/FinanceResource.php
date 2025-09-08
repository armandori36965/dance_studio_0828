<?php

namespace App\Filament\Resources\Finances;

use App\Filament\Resources\Finances\Pages\CreateFinance;
use App\Filament\Resources\Finances\Pages\EditFinance;
use App\Filament\Resources\Finances\Pages\ListFinances;
use App\Filament\Resources\Finances\Pages\ViewFinance;
use App\Filament\Resources\Finances\Schemas\FinanceForm;
use App\Filament\Resources\Finances\Schemas\FinanceInfolist;
use App\Filament\Resources\Finances\Tables\FinancesTable;
use App\Models\Finance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FinanceResource extends Resource
{
    protected static ?string $model = Finance::class;

    // 設定導航圖示
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    // 設定導航標籤
    protected static ?string $navigationLabel = '財務';

    // 設定模型標籤（單數）
    protected static ?string $modelLabel = '財務';

    // 設定模型標籤（複數）
    protected static ?string $pluralModelLabel = '財務';

    // 設定排序順序
    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('fields.finances');
    }

    public static function getModelLabel(): string
    {
        return __('fields.finance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fields.finances');
    }

    public static function form(Schema $schema): Schema
    {
        return FinanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FinanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancesTable::configure($table);
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
            'index' => ListFinances::route('/'),
            'create' => CreateFinance::route('/create'),
            'view' => ViewFinance::route('/{record}'),
            'edit' => EditFinance::route('/{record}/edit'),
        ];
    }
}
