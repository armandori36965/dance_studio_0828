<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\DateTimePicker;
use Filament\Support\Enums\MaxWidth;
use Carbon\Carbon;
use App\Filament\Resources\Courses\Schemas\CourseForm;
use App\Models\Course;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput as FormTextInput;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Forms\Components\DateTimePicker as FormDateTimePicker;
use Filament\Forms\Components\DatePicker;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['campus', 'teacher', 'assistant']))
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

                // 助教 - 預設顯示
                TextColumn::make('assistant.name')
                    ->label('助教')
                    ->sortable()
                    ->placeholder('未指派')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->action(function ($record) {
                        return redirect()->route('filament.admin.resources.courses.view', $record);
                    }),

                // 學員數 - 預設顯示
                TextColumn::make('student_count')
                    ->label(__('fields.student_count'))
                    ->getStateUsing(function ($record) {
                        // 動態計算實際報名的學生數量
                        return $record->students()->count();
                    })
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

                    // 完全複製課程批量動作
                    BulkAction::make('duplicate_complete')
                        ->label('複製課程')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('secondary')
                        ->fillForm(function (Collection $records): array {
                            // 從第一個選定的課程取得預設資料
                            $firstRecord = $records->first();
                            return [
                                'new_name' => $firstRecord->name . ' - 副本',
                                'level' => $firstRecord->level ?? 'beginner',
                                'duration' => $firstRecord->duration ?? 1,
                                'pricing_type' => $firstRecord->pricing_type ?? 'per_session',
                                'price' => $firstRecord->price ?? 0,
                                'start_time' => $firstRecord->start_time ? $firstRecord->start_time->format('H:i') : '09:00',
                                'end_time' => $firstRecord->end_time ? $firstRecord->end_time->format('H:i') : '10:00',
                            ];
                        })
                        ->form([
                            FormTextInput::make('new_name')
                                ->label('新課程名稱')
                                ->required()
                                ->helperText('例如：從「國-午1」改為「國-午1副本」'),

                            FormSelect::make('level')
                                ->label('課程等級（可選）')
                                ->options([
                                    'beginner' => '初級',
                                    'intermediate' => '中級',
                                    'advanced' => '高級',
                                    'competition' => '比賽隊',
                                ])
                                ->helperText('留空維持原課程等級'),

                            FormTextInput::make('duration')
                                ->label('課堂時數（可選）')
                                ->numeric()
                                ->step(0.5)
                                ->minValue(0.5)
                                ->suffix('小時')
                                ->helperText('留空維持原課程時數（以0.5小時為單位）'),

                            FormSelect::make('teacher_id')
                                ->label('新授課老師（可選）')
                                ->options(function () {
                                    return User::canTeach()->orderBy('name')->pluck('name', 'id');
                                })
                                ->searchable()
                                ->helperText('留空維持原老師'),

                            FormSelect::make('assistant_id')
                                ->label('新助教（可選）')
                                ->options(function () {
                                    return User::canTeach()->orderBy('name')->pluck('name', 'id');
                                })
                                ->searchable()
                                ->helperText('留空維持原助教'),

                            FormTextInput::make('start_time')
                                ->label('開始時間')
                                ->required()
                                ->placeholder('14:30')
                                ->mask('99:99')
                                ->regex('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/')
                                ->helperText('格式：HH:MM，例如 14:30（只調整時間，日期保持原課程設定）'),

                            FormTextInput::make('end_time')
                                ->label('結束時間')
                                ->required()
                                ->placeholder('16:00')
                                ->mask('99:99')
                                ->regex('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/')
                                ->helperText('格式：HH:MM，例如 16:00（只調整時間，日期保持原課程設定）'),

                            FormSelect::make('pricing_type')
                                ->label('價格計算方式（可選）')
                                ->options([
                                    'per_session' => '每堂課計費',
                                    'per_student' => '依報名人數計費',
                                ])
                                ->helperText('留空維持原計費方式'),

                            FormTextInput::make('price')
                                ->label('課程價格（可選）')
                                ->numeric()
                                ->prefix('NT$')
                                ->helperText('留空維持原課程價格'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $successCount = 0;

                            foreach ($records as $record) {
                                try {
                                    // 複製課程基本資料
                                    $duplicate = $record->replicate();

                                    // 設定新的課程資料（可選欄位如果為空則使用原值）
                                    $duplicate->name = $data['new_name'];
                                    $duplicate->level = $data['level'] ?? $record->level;
                                    $duplicate->duration = $data['duration'] ?? $record->duration;
                                    $duplicate->pricing_type = $data['pricing_type'] ?? $record->pricing_type;
                                    $duplicate->price = $data['price'] ?? $record->price;

                                    // 處理時間：將 HH:MM 格式結合原課程的日期
                                    if ($record->start_time && $record->end_time) {
                                        $originalDate = $record->start_time->toDateString(); // 取得原日期

                                        $duplicate->start_time = Carbon::parse($originalDate . ' ' . $data['start_time'], 'Asia/Taipei');
                                        $duplicate->end_time = Carbon::parse($originalDate . ' ' . $data['end_time'], 'Asia/Taipei');
                                    }

                                    // 調整老師/助教（若指定）
                                    if (!empty($data['teacher_id'])) {
                                        $duplicate->teacher_id = $data['teacher_id'];
                                    }
                                    if (!empty($data['assistant_id'])) {
                                        $duplicate->assistant_id = $data['assistant_id'];
                                    }

                                    // 預設為停用狀態，讓用戶檢查後再啟用
                                    $duplicate->is_active = false;

                                    // 生成新的排序值
                                    $maxSortOrder = Course::max('sort_order') ?? 0;
                                    $duplicate->sort_order = $maxSortOrder + 1;

                                    // 暫時禁用事件以避免自動生成 CourseSession
                                    Course::withoutEvents(function () use ($duplicate) {
                                        $duplicate->save();
                                    });

                                    // 手動複製所有現有的 CourseSession
                                    $originalSessions = $record->sessions()->orderBy('sort_order')->get();
                                    foreach ($originalSessions as $session) {
                                        $newSession = $session->replicate();
                                        $newSession->course_id = $duplicate->id;

                                        // 使用更簡單的時間替換邏輯：
                                        // 保持原課程排定的日期，但使用新課程的時間
                                        $sessionDate = $session->start_time->toDateString();

                                        $newSession->start_time = Carbon::parse($sessionDate . ' ' . $duplicate->start_time->format('H:i'), 'Asia/Taipei');
                                        $newSession->end_time = Carbon::parse($sessionDate . ' ' . $duplicate->end_time->format('H:i'), 'Asia/Taipei');

                                        // 繼承老師和助教設定
                                        $newSession->teacher_id = $duplicate->teacher_id;
                                        $newSession->assistant_id = $duplicate->assistant_id;

                                        $newSession->save();
                                    }

                                    // 記錄成功
                                    Log::info("複製課程成功: {$record->name} → {$duplicate->name} (ID: {$duplicate->id}), 複製了 " . $originalSessions->count() . " 個課程排定");
                                    $successCount++;

                                } catch (\Exception $e) {
                                    Log::error("複製課程失敗: {$record->name} - " . $e->getMessage());
                                }
                            }

                            // 發送通知
                            if ($successCount > 0) {
                                Notification::make()
                                    ->title('複製課程成功')
                                    ->body("已成功複製 {$successCount} 個課程（包含所有課程排定），預設為停用狀態，請檢查後啟用。")
                                    ->success()
                                    ->duration(5000)
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('複製失敗')
                                    ->body('無法複製任何課程，請檢查日誌以了解詳情。')
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('批量完全複製課程')
                        ->modalDescription('這將完全複製選中的課程（包含所有已建立的課程排定），您可以調整各項設定。')
                        ->modalSubmitActionLabel('確認複製')
                        ->modalCancelActionLabel('取消')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->extremePaginationLinks() // 改善分頁顯示
            ->paginated([10, 25, 50, 100]) // 設定每頁顯示筆數選項
            ->defaultPaginationPageOption(10) // 預設每頁顯示10筆
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
