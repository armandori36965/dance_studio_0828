<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Campus;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Carbon\Carbon;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label(__('fields.course_name'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('fields.course_description'))
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label(__('fields.course_price'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('NT$'),
                TextInput::make('student_count')
                    ->label(__('fields.student_count'))
                    ->disabled()
                    ->default(0)
                    ->helperText('自動計算目前報名該課程的學員數'),
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
                Select::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->options(function () {
                        return Campus::orderBy('sort_order', 'asc')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),
                Select::make('teacher_id')
                    ->label('授課老師')
                    ->options(function () {
                        return User::whereHas('role', function ($query) {
                            $query->where('name', '教師');
                        })->orderBy('name', 'asc')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('選擇授課老師'),
                Select::make('level')
                    ->label(__('fields.course_level'))
                    ->options([
                        'beginner' => '初級',
                        'intermediate' => '中級',
                        'advanced' => '高級',
                        'competition' => '比賽隊',
                    ])
                    ->required()
                    ->default('beginner'),
                Toggle::make('is_active')
                    ->label(__('fields.is_active'))
                    ->required()
                    ->default(true),
            ]);
    }
}
