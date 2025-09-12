<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use App\Filament\Resources\Attendances\Schemas\AttendanceForm;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Lang;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'course', 'campus']))
            ->columns([
                TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // 學員姓名
                TextColumn::make('user.name')
                    ->label(__('fields.student_name'))
                    ->searchable()
                    ->sortable(),

                // 課程名稱
                TextColumn::make('course.name')
                    ->label(__('fields.course_name'))
                    ->searchable()
                    ->sortable(),

                // 上課日期
                TextColumn::make('date')
                    ->label(__('fields.class_date'))
                    ->date()
                    ->sortable(),

                // 簽到時間
                TextColumn::make('check_in_time')
                    ->label(__('fields.check_in_time'))
                    ->dateTime('H:i')
                    ->sortable(),

                // 簽退時間
                TextColumn::make('check_out_time')
                    ->label(__('fields.check_out_time'))
                    ->dateTime('H:i')
                    ->sortable(),

                // 出勤狀態
                TextColumn::make('status')
                    ->label(__('fields.attendance_status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info',
                        default => 'gray',
                    }),

                // 備註
                TextColumn::make('notes')
                    ->label(__('fields.notes'))
                    ->limit(50)
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
                SelectFilter::make('status')
                    ->label(__('fields.attendance_status'))
                    ->options([
                        'present' => '出席',
                        'absent' => '缺席',
                        'late' => '遲到',
                        'excused' => '請假',
                    ]),
                SelectFilter::make('course_id')
                    ->label(__('fields.course_name'))
                    ->relationship('course', 'name'),
                SelectFilter::make('user_id')
                    ->label(__('fields.student_name'))
                    ->relationship('user', 'name'),
            ])
            ->recordActions([
                EditAction::make()
                    ->form(fn (Schema $form) => AttendanceForm::configure($form)),
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
