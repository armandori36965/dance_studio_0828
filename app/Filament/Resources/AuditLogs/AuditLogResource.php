<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\CreateAuditLog;
use App\Filament\Resources\AuditLogs\Pages\EditAuditLog;
use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Resources\AuditLogs\Pages\ViewAuditLog;
use App\Filament\Resources\AuditLogs\Schemas\AuditLogForm;
use App\Filament\Resources\AuditLogs\Schemas\AuditLogInfolist;
use App\Filament\Resources\AuditLogs\Tables\AuditLogsTable;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    // 設定導航圖示
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    // 設定導航標籤
    protected static ?string $navigationLabel = '審計日誌';

    // 設定模型標籤（單數）
    protected static ?string $modelLabel = '審計日誌';

    // 設定模型標籤（複數）
    protected static ?string $pluralModelLabel = '審計日誌';

    // 設定排序順序
    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationLabel(): string
    {
        return __('fields.audit_logs');
    }

    public static function getModelLabel(): string
    {
        return __('fields.audit_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('fields.audit_logs');
    }

    public static function form(Schema $schema): Schema
    {
        return AuditLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
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
            'index' => ListAuditLogs::route('/'),
            'create' => CreateAuditLog::route('/create'),
            'view' => ViewAuditLog::route('/{record}'),
            'edit' => EditAuditLog::route('/{record}/edit'),
        ];
    }
}
