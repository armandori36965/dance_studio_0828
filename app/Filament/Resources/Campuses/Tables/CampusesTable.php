<?php

namespace App\Filament\Resources\Campuses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CampusesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.campus_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label(__('fields.address'))
                    ->searchable()
                    ->limit(50),
                TextColumn::make('phone')
                    ->label(__('fields.phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('fields.email_address'))
                    ->searchable()
                    ->limit(50),
                IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 啟用狀態過濾器
                TernaryFilter::make('is_active')
                    ->label('啟用狀態')
                    ->placeholder('所有狀態')
                    ->trueLabel('已啟用')
                    ->falseLabel('已停用'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('查看'),
                EditAction::make()
                    ->label('編輯'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('刪除選中'),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
    }
}
