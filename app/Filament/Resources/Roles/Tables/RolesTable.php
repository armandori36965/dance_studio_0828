<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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

                // 是否需要校區欄位
                TextColumn::make('requires_campus')
                    ->label('需要校區')
                    ->formatStateUsing(fn ($state) => $state ? '是' : '否')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

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
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 更新時間欄位
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 過濾器設定
            ])
            ->recordActions([
                // 記錄操作按鈕
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                // 工具列操作按鈕
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->extremePaginationLinks() // 改善分頁顯示
            ->paginated([10, 25, 50, 100]) // 設定每頁顯示筆數選項
            ->defaultPaginationPageOption(10) // 預設每頁顯示10筆
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
