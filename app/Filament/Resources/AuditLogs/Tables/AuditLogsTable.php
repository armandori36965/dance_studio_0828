<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Lang;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 用戶欄位
                TextColumn::make('user.name')
                    ->label(__('fields.user_name'))
                    ->searchable()
                    ->sortable(),

                // 操作欄位
                TextColumn::make('action')
                    ->label(__('fields.action_type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'restored' => 'info',
                        default => 'gray',
                    }),

                // 模型類型欄位
                TextColumn::make('model_type')
                    ->label(__('fields.model_type'))
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable()
                    ->sortable(),

                // 模型ID欄位
                TextColumn::make('model_id')
                    ->label(__('fields.model_id'))
                    ->numeric()
                    ->sortable(),

                // IP地址欄位
                TextColumn::make('ip_address')
                    ->label(__('fields.ip_address'))
                    ->searchable()
                    ->sortable(),

                // 建立時間欄位
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 操作類型過濾器
                SelectFilter::make('action')
                    ->label(__('fields.action_type'))
                    ->options([
                        'created' => __('status.audit.created'),
                        'updated' => __('status.audit.updated'),
                        'deleted' => __('status.audit.deleted'),
                        'restored' => __('status.audit.restored'),
                    ]),

                // 模型類型過濾器
                SelectFilter::make('model_type')
                    ->label(__('fields.model_type'))
                    ->options([
                                    'User' => __('fields.users'),
            'Course' => __('fields.courses'),
            'Equipment' => __('fields.equipment'),
            'Campus' => __('fields.campuses'),
                    ]),
            ])
            ->recordActions([
                // 記錄操作按鈕
                ViewAction::make(),
            ])
            ->toolbarActions([
                // 工具列操作按鈕
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
