<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Role;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use App\Filament\Resources\Users\Schemas\UserForm;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 用戶姓名欄位
                TextColumn::make('name')
                    ->label(__('fields.user_name'))
                    ->searchable()
                    ->sortable(),

                // 電子郵件欄位
                TextColumn::make('email')
                    ->label(__('fields.email'))
                    ->searchable()
                    ->sortable(),

                // 角色欄位
                TextColumn::make('role.name')
                    ->label(__('fields.role'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                // 電子郵件驗證時間
                TextColumn::make('email_verified_at')
                    ->label(__('fields.email_verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 建立時間
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 更新時間
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role_id')
                    ->label(__('fields.role'))
                    ->relationship('role', 'name'),
                SelectFilter::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->relationship('campus', 'name'),
            ])
            ->recordActions([
                // 記錄操作按鈕
                EditAction::make()
                    ->form(fn (Schema $form) => UserForm::configure($form)),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                // 工具列操作按鈕
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
