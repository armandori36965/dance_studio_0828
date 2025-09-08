<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\Course;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // 課程選擇
                Select::make('course_id')
                    ->label(__('fields.course_name'))
                    ->options(Course::pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                // 學生選擇
                Select::make('user_id')
                    ->label(__('fields.student_name'))
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                // 日期選擇器 - 使用完整的 DateTimePicker
                DateTimePicker::make('date')
                    ->label(__('fields.class_date'))
                    ->required()
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->default(now())
                    ->placeholder(__('actions.choose') . __('fields.class_date')),

                // 簽到時間 - 使用完整的 DateTimePicker
                DateTimePicker::make('check_in_time')
                    ->label(__('fields.check_in_time'))
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->default(now())
                    ->placeholder(__('actions.choose') . __('fields.check_in_time')),

                // 簽退時間 - 使用完整的 DateTimePicker
                DateTimePicker::make('check_out_time')
                    ->label(__('fields.check_out_time'))
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->default(now())
                    ->placeholder(__('actions.choose') . __('fields.check_out_time')),

                // 備註
                Textarea::make('notes')
                    ->label(__('fields.notes'))
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
