<?php

namespace App\Filament\Resources\SchoolEvents\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use App\Filament\Resources\SchoolEvents\Schemas\SchoolEventForm;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Lang;

class SchoolEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                                // 核心欄位 - 不可切換
                TextColumn::make('title')
                    ->label('標題')
                    ->searchable()
                    ->sortable()
                    ->toggleable(false),

                TextColumn::make('start_time')
                    ->label('開始時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(false),

                TextColumn::make('category')
                    ->label('類型')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'course' => 'primary',
                        'performance' => 'success',
                        'meeting' => 'warning',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'course' => __('fields.course_activity'),
                        'performance' => __('fields.performance_activity'),
                        'meeting' => __('fields.meeting_activity'),
                        'other' => __('fields.other_activity'),
                        default => $state,
                    })
                    ->toggleable(false),

                TextColumn::make('status')
                    ->label('狀態')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'todo' => 'warning',
                        'pending' => 'warning', // 向後相容舊資料
                        'completed' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => __('fields.status_event_active'),
                        'inactive' => __('fields.status_event_inactive'),
                        'todo' => __('fields.status_event_todo'),
                        'pending' => __('fields.status_event_todo'), // 向後相容舊資料
                        'completed' => __('fields.status_event_completed'),
                        default => $state,
                    })
                    ->toggleable(false),

                // 可選欄位 - 可切換
                TextColumn::make('description')
                    ->label('描述')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('end_time')
                    ->label('結束時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('location')
                    ->label('地點')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('建立者')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('更新時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(__('fields.event_type'))
                    ->options([
                        'course' => __('fields.course_activity'),
                        'performance' => __('fields.performance_activity'),
                        'meeting' => __('fields.meeting_activity'),
                        'other' => __('fields.other_activity'),
                    ]),
                SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options([
                        'active' => __('fields.status_event_active'),
                        'inactive' => __('fields.status_event_inactive'),
                        'todo' => __('fields.status_event_todo'),
                        'completed' => __('fields.status_event_completed'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->icon('heroicon-o-pencil-square') // 使用鉛筆方塊圖示
                    ->form(fn (Schema $form) => SchoolEventForm::configure($form)),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('60s') // 可選：添加輪詢更新
            ->extremePaginationLinks() // 改善分頁顯示
            ->defaultSort('start_time', 'desc'); // 預設按開始時間排序
    }
}
