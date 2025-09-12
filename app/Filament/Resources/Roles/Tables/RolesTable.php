<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // 角色名稱欄位
                TextColumn::make('name')
                    ->label(__('fields.role_name'))
                    ->searchable()
                    ->sortable(),

                // 角色描述欄位
                TextColumn::make('description')
                    ->label(__('fields.role_description'))
                    ->searchable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                // 權限數量欄位
                TextColumn::make('permission_count')
                    ->label(__('fields.permission_count'))
                    ->getStateUsing(fn ($record) => count($record->permissions ?? []) . ' ' . __('fields.count')),

                // 用戶數量欄位
                TextColumn::make('user_count')
                    ->label(__('fields.user_count'))
                    ->getStateUsing(fn ($record) => count($record->users ?? []) . ' ' . __('fields.count')),

                // 建立時間欄位
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 更新時間欄位
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 過濾器設定
            ])
            ->recordActions([
                // 記錄操作按鈕
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                // 工具列操作按鈕
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
