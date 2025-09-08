<?php

namespace App\Filament\Resources\Finances\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FinanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label(__('fields.finance_title'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('fields.finance_description'))
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('type')
                    ->label(__('fields.finance_type'))
                    ->options(['income' => __('fields.income'), 'expense' => __('fields.expense')])
                    ->required(),
                TextInput::make('amount')
                    ->label(__('fields.amount'))
                    ->required()
                    ->numeric()
                    ->prefix('NT$'),
                TextInput::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->required()
                    ->numeric(),
                TextInput::make('course_id')
                    ->label(__('fields.course_name'))
                    ->numeric()
                    ->default(null),
                TextInput::make('user_id')
                    ->label(__('fields.user_name'))
                    ->numeric()
                    ->default(null),
                // 交易日期 - 使用完整的 DateTimePicker
                DateTimePicker::make('transaction_date')
                    ->label(__('fields.transaction_date'))
                    ->required()
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->default(now()),
                TextInput::make('payment_method')
                    ->label(__('fields.payment_method'))
                    ->default(null),
                TextInput::make('reference_number')
                    ->label(__('fields.reference_number'))
                    ->default(null),
                Textarea::make('notes')
                    ->label(__('fields.notes'))
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
