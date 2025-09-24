<?php

namespace App\Filament\Resources\Equipment\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use App\Filament\Resources\Equipment\Schemas\EquipmentForm;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Lang;

class EquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['campus']))
            ->columns([
                TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('fields.equipment_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serial_number')
                    ->label(__('fields.serial_number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'in_use' => 'warning',
                        'maintenance' => 'info',
                        'broken' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('campus.name')
                    ->label(__('fields.campus_name'))
                    ->sortable(),
                TextColumn::make('purchase_date')
                    ->label(__('fields.purchase_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('purchase_price')
                    ->label(__('fields.purchase_price'))
                    ->money('TWD')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options([
                        'available' => '可用',
                        'in_use' => '使用中',
                        'maintenance' => '維護中',
                        'broken' => '故障',
                    ]),
                SelectFilter::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->relationship('campus', 'name'),
            ])
            ->recordActions([
                EditAction::make()
                    ->form(fn (Schema $form) => EquipmentForm::configure($form)),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->extremePaginationLinks() // 顯示第一頁和最後一頁按鈕
            ->paginated([10, 25, 50, 100]) // 設定每頁顯示筆數選項
            ->defaultPaginationPageOption(10) // 預設每頁顯示10筆
            ->paginationPageOptions([10, 25, 50, 100]) // 確保分頁選項正確設定
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
