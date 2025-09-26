<?php

namespace App\Filament\Resources\Finances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use App\Filament\Resources\Finances\Schemas\FinanceForm;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['campus', 'course', 'user']))
            ->columns([
                TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')
                    ->label(__('fields.finance_title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('fields.finance_type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label(__('fields.amount'))
                    ->money('TWD')
                    ->sortable(),
                TextColumn::make('campus.name')
                    ->label(__('fields.campus_name'))
                    ->sortable(),
                TextColumn::make('course.name')
                    ->label(__('fields.course_name'))
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('fields.user_name'))
                    ->sortable(),
                TextColumn::make('transaction_date')
                    ->label(__('fields.transaction_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label(__('fields.payment_method'))
                    ->sortable(),
                TextColumn::make('reference_number')
                    ->label(__('fields.reference_number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('fields.finance_type'))
                    ->options([
                        'income' => '收入',
                        'expense' => '支出',
                    ]),
                SelectFilter::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->relationship('campus', 'name'),
                SelectFilter::make('category')
                    ->label(__('fields.category'))
                    ->options([
                        'tuition' => '學費',
                        'equipment' => '設備費用',
                        'maintenance' => '維護費用',
                        'salary' => '薪資',
                        'other' => '其他',
                    ]),
                SelectFilter::make('status')
                    ->label(__('fields.status'))
                    ->options([
                        'completed' => '已完成',
                        'pending' => '待處理',
                        'cancelled' => '已取消',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->form(fn (Schema $form) => FinanceForm::configure($form)),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
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
