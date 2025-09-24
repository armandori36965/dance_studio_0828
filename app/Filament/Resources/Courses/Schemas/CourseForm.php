<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Campus;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // 第一行：課程名稱* 校區名稱*
                TextInput::make('name')
                    ->label(__('fields.course_name'))
                    ->required()
                    ->columnSpan(1),
                Select::make('campus_id')
                    ->label(__('fields.campus_name'))
                    ->options(function () {
                        return Campus::orderBy('sort_order', 'asc')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->columnSpan(1),

                // 第二行：課程描述（全寬）
                Textarea::make('description')
                    ->label(__('fields.course_description'))
                    ->default(null)
                    ->columnSpanFull(),

                // 第三行：課程等級* 課堂時數*
                Select::make('level')
                    ->label(__('fields.course_level'))
                    ->options([
                        'beginner' => '初級',
                        'intermediate' => '中級',
                        'advanced' => '高級',
                        'competition' => '比賽隊',
                    ])
                    ->required()
                    ->default('beginner')
                    ->columnSpan(1),
                TextInput::make('duration')
                    ->label('課堂時數')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->suffix('小時')
                    ->helperText('每堂課的時數')
                    ->columnSpan(1),

                // 第四行：授課老師 助教
                Select::make('teacher_id')
                    ->label('授課老師')
                    ->options(function () {
                        return User::canTeach()->orderBy('name', 'asc')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('選擇授課老師')
                    ->columnSpan(1),
                Select::make('assistant_id')
                    ->label('助教')
                    ->options(function () {
                        return User::canTeach()->orderBy('name', 'asc')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('選擇助教')
                    ->columnSpan(1),


                // 價格計算方式
                Select::make('pricing_type')
                    ->label('價格計算方式')
                    ->options([
                        'per_session' => '每堂課計費',
                        'per_student' => '依報名人數計費',
                    ])
                    ->default('per_session')
                    ->required()
                    ->helperText('每堂課計費適用按課時收費，依報名人數計費適用包班收費')
                    ->columnSpan(1),

                // 單價
                TextInput::make('price')
                    ->label(__('fields.course_price'))
                    ->numeric()
                    ->default(0)
                    ->prefix('NT$')
                    ->required()
                    ->helperText('每堂課的單價或每位學生的單價')
                    ->columnSpan(1),

                // 學員數（僅在依報名人數計費時顯示）
                TextInput::make('student_count')
                    ->label(__('fields.student_count'))
                    ->numeric()
                    ->default(0)
                    ->helperText('目前報名該課程的學員數（依報名人數計費時會自動更新）')
                    ->disabled()
                    ->visible(fn ($get) => $get('pricing_type') === 'per_student')
                    ->columnSpanFull(),


                // 統一的時間欄位（根據課程類型動態調整）
                DateTimePicker::make('start_time')
                    ->label('開始時間')
                    ->required()
                    ->default(Carbon::today()->setTime(7, 0))
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->columnSpan(1)
                    ->dehydrated(true) // 總是儲存
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
                    ->default(Carbon::today()->setTime(8, 0))
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->after('start_time')
                    ->rules(['after:start_time'])
                    ->columnSpan(1)
                    ->dehydrated(true), // 總是儲存

                // 避開的校務事件
                CheckboxList::make('avoid_event_types')
                    ->label('避開的校務事件類型')
                    ->options([
                        'todo' => '代辦事項',
                        'school' => '校務',
                        'other' => '其他',
                        'national_holiday' => '國定假日',
                    ])
                    ->default(['school', 'national_holiday'])
                    ->columns(2)
                    ->required()
                    ->columnSpanFull(),

                // 是否為每周課程
                Toggle::make('is_weekly_course')
                    ->label('是否為每周課程')
                    ->default(false)
                    ->live()
                    ->columnSpanFull(),

                // 每周課程設定區塊
                Placeholder::make('weekly_course_placeholder')
                    ->label('每周課程設定')
                    ->content('')
                    ->visible(fn ($get) => $get('is_weekly_course'))
                    ->columnSpanFull(),

                TextInput::make('total_sessions')
                    ->label('課堂次數')
                    ->numeric()
                    ->default(1)
                    ->helperText('總共要重複建立幾次課程')
                    ->required(fn ($get) => $get('is_weekly_course'))
                    ->visible(fn ($get) => $get('is_weekly_course'))
                    ->columnSpanFull(),

                CheckboxList::make('weekdays')
                    ->label('每周上課日')
                    ->options([
                        '1' => '星期一',
                        '2' => '星期二',
                        '3' => '星期三',
                        '4' => '星期四',
                        '5' => '星期五',
                        '6' => '星期六',
                        '0' => '星期日',
                    ])
                    ->columns(7)
                    ->required(fn ($get) => $get('is_weekly_course'))
                    ->visible(fn ($get) => $get('is_weekly_course'))
                    ->columnSpanFull(),
            ]);
    }
}
