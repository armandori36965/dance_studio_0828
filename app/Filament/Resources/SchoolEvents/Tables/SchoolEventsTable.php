<?php

namespace App\Filament\Resources\SchoolEvents\Tables;

use App\Models\User;
use App\Models\Campus;
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
                        'national_holiday' => 'danger',
                        'periodic_assessment' => 'warning',
                        'disaster_drill' => 'info',
                        'school_anniversary' => 'success',
                        'todo' => 'primary',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'national_holiday' => __('fields.national_holiday'),
                        'periodic_assessment' => __('fields.periodic_assessment'),
                        'disaster_drill' => __('fields.disaster_drill'),
                        'school_anniversary' => __('fields.school_anniversary'),
                        'todo' => __('fields.todo'),
                        'other' => __('fields.other'),
                        default => $state,
                    })
                    ->toggleable(false),

                TextColumn::make('campus.name')
                    ->label(__('fields.campus'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) {
                            return $state;
                        }
                        // 如果是國定假日，顯示「國定假日」
                        return $record->category === 'national_holiday' ? '國定假日' : '未指定';
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
                        'national_holiday' => __('fields.national_holiday'),
                        'periodic_assessment' => __('fields.periodic_assessment'),
                        'disaster_drill' => __('fields.disaster_drill'),
                        'school_anniversary' => __('fields.school_anniversary'),
                        'todo' => __('fields.todo'),
                        'other' => __('fields.other'),
                    ]),

                SelectFilter::make('campus_id')
                    ->label(__('fields.campus'))
                    ->options(function () {
                        return Campus::orderBy('sort_order', 'asc')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->query(function ($query, array $data) {
                        if (isset($data['value']) && $data['value'] !== '') {
                            // 篩選特定校區時，同時顯示該校區事件和國定假日
                            $query->where(function ($q) use ($data) {
                                $q->where('campus_id', $data['value'])
                                  ->orWhereNull('campus_id');
                            });
                        }
                    }),
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
            ->paginated([10, 25, 50, 100]) // 設定每頁顯示筆數選項
            ->defaultPaginationPageOption(10) // 預設每頁顯示10筆
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc'); // 預設按排序欄位排序
    }
}
