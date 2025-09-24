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
                // 課程名稱 - 預設顯示
                TextColumn::make('name')
                    ->label(__('fields.course_name'))
                    ->searchable()
                    ->sortable()
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.courses.view', $record);
                    }),

                // 課程等級 - 預設顯示
                TextColumn::make('level')
                    ->label(__('fields.course_level'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beginner' => '初級',
                        'intermediate' => '中級',
                        'advanced' => '高級',
                        'competition' => '比賽隊',
                        default => $state,
                    })
                    ->sortable()
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.courses.view', $record);
                    }),

                // 授課老師 - 預設顯示
                TextColumn::make('teacher.name')
                    ->label('授課老師')
                    ->sortable()
                    ->placeholder('未指派')
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.courses.view', $record);
                    }),

                // 學員數 - 預設顯示
                TextColumn::make('student_count')
                    ->label(__('fields.student_count'))
                    ->sortable()
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.courses.view', $record);
                    }),

                // 總共堂數 - 預設顯示
                TextColumn::make('total_sessions')
                    ->label('總共堂數')
                    ->formatStateUsing(fn ($state) => $state ? $state . '堂' : '-')
                    ->sortable()
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.courses.view', $record);
                    }),

                // 上課週期 - 預設顯示
                TextColumn::make('weekdays')
                    ->label('上課週期')
                    ->getStateUsing(function ($record) {
                        // 如果沒有設定週期課程，直接返回
                        if (!$record->is_weekly_course) {
                            return '-';
                        }

                        $state = $record->weekdays;

                        // 如果沒有上課日資料，返回
                        if (!$state || !is_array($state) || empty($state)) {
                            return '-';
                        }

                        $weekdayNames = [
                            '0' => '日',
                            '1' => '一',
                            '2' => '二',
                            '3' => '三',
                            '4' => '四',
                            '5' => '五',
                            '6' => '六',
                        ];

                        $weekdays = array_map(function ($day) use ($weekdayNames) {
                            return $weekdayNames[$day] ?? $day;
                        }, $state);

                        return '週' . implode('、週', $weekdays);
                    })
                    ->sortable(false)
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.courses.view', $record);
                    }),

                // 校區名稱 - 預設顯示
                TextColumn::make('campus.name')
                    ->label(__('fields.campus_name'))
                    ->sortable()
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.courses.view', $record);
                    }),

                // 課程價格 - 隱藏
                TextColumn::make('price')
                    ->label(__('fields.course_price'))
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 課程時長 - 隱藏
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
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: true),

                // 啟用狀態 - 隱藏
                IconColumn::make('is_active')
                    ->label(__('fields.is_active'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 建立時間 - 隱藏
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 更新時間 - 隱藏
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
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
                    ->options(function () {
                        return \App\Models\Campus::orderBy('sort_order', 'asc')->pluck('name', 'id');
                    })
                    ->searchable(),
                SelectFilter::make('is_weekly_course')
                    ->label('課程類型')
                    ->options([
                        '1' => '週期課程',
                        '0' => '單次課程',
                    ]),
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
            ->extremePaginationLinks() // 顯示第一頁和最後一頁按鈕
            ->paginated([10, 25, 50, 100]) // 設定每頁顯示筆數選項
            ->defaultPaginationPageOption(10) // 預設每頁顯示10筆
            ->paginationPageOptions([10, 25, 50, 100]) // 確保分頁選項正確設定
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
