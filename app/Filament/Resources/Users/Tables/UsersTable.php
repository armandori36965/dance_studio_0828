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

                // 班級欄位
                TextColumn::make('class')
                    ->label('班級')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: false),

                // 校區欄位
                TextColumn::make('campus.name')
                    ->label(__('fields.campus'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                // 角色欄位
                TextColumn::make('role.name')
                    ->label(__('fields.role'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                // 電子郵件欄位
                TextColumn::make('email')
                    ->label(__('fields.email'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 學校課程欄位
                TextColumn::make('school_courses')
                    ->label('學校課程')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->courses) {
                            return [];
                        }

                        $courses = $record->courses->load('campus');
                        return $courses->filter(function ($course) {
                            return $course->campus && $course->campus->type === 'school';
                        })->pluck('name')->toArray();
                    })
                    ->separator(', ')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (is_array($state) && count($state) > 0) {
                            return implode(', ', $state);
                        }
                        return null;
                    })
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 補習班課程欄位
                TextColumn::make('cram_school_courses')
                    ->label('補習班課程')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->courses) {
                            return [];
                        }

                        $courses = $record->courses->load('campus');
                        return $courses->filter(function ($course) {
                            return $course->campus && $course->campus->type === 'cram_school';
                        })->pluck('name')->toArray();
                    })
                    ->separator(', ')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (is_array($state) && count($state) > 0) {
                            return implode(', ', $state);
                        }
                        return null;
                    })
                    ->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),


                // 建立時間
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 更新時間
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role_id')
                    ->label(__('fields.role'))
                    ->options(function () {
                        return \App\Models\Role::orderBy('sort_order', 'asc')->pluck('name', 'id');
                    })
                    ->searchable(),
                SelectFilter::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->options(function () {
                        return \App\Models\Campus::orderBy('sort_order', 'asc')->pluck('name', 'id');
                    })
                    ->searchable(),
                SelectFilter::make('class')
                    ->label('班級')
                    ->options(function () {
                        return \App\Models\User::whereNotNull('class')
                            ->where('class', '!=', '')
                            ->distinct()
                            ->pluck('class', 'class')
                            ->sort();
                    })
                    ->searchable(),
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
            ])
            ->extremePaginationLinks() // 改善分頁顯示
            ->paginated([10, 25, 50, 100]) // 設定每頁顯示筆數選項
            ->defaultPaginationPageOption(10) // 預設每頁顯示10筆
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
