<?php

namespace App\Filament\Resources\SchoolEvents\Schemas;

use App\Models\User;
use App\Models\Campus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;
use Carbon\Carbon;

class SchoolEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->label(__('fields.event_type'))
                    ->searchable()
                    ->placeholder(__('fields.select_type'))
                    ->options([
                        'todo' => __('fields.todo'),
                        'school' => __('fields.school'),
                        'other' => __('fields.other'),
                        'national_holiday' => __('fields.national_holiday'),
                    ])
                    ->required(),

                Select::make('campus_id')
                    ->label('校區')
                    ->options(function () {
                        return Campus::orderBy('sort_order', 'asc')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('請選擇校區')
                    ->helperText('留空表示此事件為國定假日，適用於所有校區'),

                Textarea::make('description')
                    ->label('事件描述')
                    ->maxLength(1000)
                    ->columnSpanFull(),

                DateTimePicker::make('start_time')
                    ->label('開始時間')
                    ->required()
                    ->default(Carbon::now())
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // 當開始時間改變時，自動設定結束時間為開始時間後1小時
                        if ($state) {
                            $endTime = Carbon::parse($state)->addHour();
                            $set('end_time', $endTime->format('Y-m-d H:i:s'));
                        }
                    }),

                DateTimePicker::make('end_time')
                    ->label('結束時間')
                    ->required()
                    ->default(Carbon::now()->addHour())
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->after('start_time')
                    ->rules(['after:start_time']),





            ]);
    }
}
