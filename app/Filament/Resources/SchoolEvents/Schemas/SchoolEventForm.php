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
                TextInput::make('title')
                    ->label(__('fields.event_title'))
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label(__('fields.event_description'))
                    ->maxLength(1000)
                    ->columnSpanFull(),

                DateTimePicker::make('start_time')
                    ->label(__('fields.start_time'))
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
                    ->label(__('fields.end_time'))
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

                TextInput::make('location')
                    ->label(__('fields.location'))
                    ->maxLength(255),

                Select::make('category')
                    ->label(__('fields.event_type'))
                    ->searchable()
                    ->options([
                        'course' => __('fields.course_activity'),
                        'performance' => __('fields.performance_activity'),
                        'meeting' => __('fields.meeting_activity'),
                        'other' => __('fields.other_activity'),
                    ])
                    ->required(),

                Select::make('status')
                    ->label(__('fields.status'))
                    ->searchable()
                    ->options([
                        'active' => __('fields.status_event_active'),
                        'inactive' => __('fields.status_event_inactive'),
                        'todo' => __('fields.status_event_todo'),
                        'completed' => __('fields.status_event_completed'),
                    ])
                    ->required()
                    ->default('active'),

                Select::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->options(Campus::pluck('name', 'id'))
                    ->searchable()
                    ->required(),





            ]);
    }
}
