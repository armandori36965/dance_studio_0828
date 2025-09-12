<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use App\Filament\Resources\Courses\Schemas\CourseForm;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('fields.course_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label(__('fields.course_price'))
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0))
                    ->sortable(),
                TextColumn::make('duration')
                    ->label(__('fields.course_duration'))
                    ->getStateUsing(function ($record) {
                        if ($record->start_time && $record->end_time) {
                            $start = \Carbon\Carbon::parse($record->start_time);
                            $end = \Carbon\Carbon::parse($record->end_time);

                            // 使用 timestamp 計算，確保正確
                            $startTimestamp = $start->timestamp;
                            $endTimestamp = $end->timestamp;

                            if ($endTimestamp > $startTimestamp) {
                                $minutes = ($endTimestamp - $startTimestamp) / 60;
                                $hours = floor($minutes / 60);
                                $remainingMinutes = $minutes % 60;

                                if ($hours > 0) {
                                    return $hours . '小時' . ($remainingMinutes > 0 ? $remainingMinutes . '分鐘' : '');
                                } else {
                                    return round($minutes) . '分鐘';
                                }
                            } else {
                                return '時間設定錯誤';
                            }
                        }
                        return '-';
                    })
                    ->sortable(false),
                TextColumn::make('student_count')
                    ->label(__('fields.student_count'))
                    ->sortable(),
                TextColumn::make('campus.name')
                    ->label(__('fields.campus_name'))
                    ->sortable(),
                TextColumn::make('level')
                    ->label(__('fields.course_level'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beginner' => '初級',
                        'intermediate' => '中級',
                        'advanced' => '高級',
                        'competition' => '比賽隊',
                        default => $state,
                    })
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->label(__('fields.course_level'))
                    ->options([
                        'beginner' => '初級',
                        'intermediate' => '中級',
                        'advanced' => '高級',
                        'competition' => '比賽隊',
                    ]),
                SelectFilter::make('is_active')
                    ->label(__('fields.is_active'))
                    ->options([
                        '1' => '啟用',
                        '0' => '停用',
                    ]),
                SelectFilter::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->relationship('campus', 'name'),
            ])
            ->recordActions([
                EditAction::make()
                    ->form(fn (Schema $form) => CourseForm::configure($form)),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
