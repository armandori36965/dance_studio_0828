<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('課程名稱'),
                TextEntry::make('price')
                    ->label('課程單價')
                    ->money('TWD'),
                TextEntry::make('pricing_type')
                    ->label('價格計算方式')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'per_session' => '每堂課計費',
                        'per_student' => '依報名人數計費',
                        default => $state,
                    }),
                TextEntry::make('total_price')
                    ->label('總價格')
                    ->getStateUsing(fn ($record) => $record->getTotalPrice())
                    ->money('TWD')
                    ->helperText('根據價格計算方式和實際堂數/學員數計算'),
                TextEntry::make('duration')
                    ->label('課程時長')
                    ->numeric(),
                TextEntry::make('max_students')
                    ->label('最大學員數')
                    ->numeric(),
                TextEntry::make('campus.name')
                    ->label('所屬校區'),
                TextEntry::make('level')
                    ->label('課程等級')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beginner' => '初級',
                        'intermediate' => '中級',
                        'advanced' => '高級',
                        'competition' => '比賽隊',
                        default => $state,
                    }),

                // 週期課程相關欄位
                IconEntry::make('is_weekly_course')
                    ->label('是否為週期課程')
                    ->boolean(),

                TextEntry::make('total_sessions')
                    ->label('總共堂數')
                    ->formatStateUsing(fn ($state) => $state ? $state . '堂' : '-'),

                TextEntry::make('weekdays')
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
                    }),

                TextEntry::make('teacher.name')
                    ->label('授課老師')
                    ->placeholder('未指派'),

                TextEntry::make('student_count')
                    ->label('目前學員數')
                    ->numeric(),

                // 時間相關欄位
                TextEntry::make('start_time')
                    ->label('開始時間')
                    ->dateTime('Y-m-d H:i'),

                TextEntry::make('end_time')
                    ->label('結束時間')
                    ->dateTime('Y-m-d H:i'),

                TextEntry::make('description')
                    ->label('課程描述')
                    ->columnSpanFull(),

                IconEntry::make('is_active')
                    ->label('啟用狀態')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i'), // 使用24小時制格式
                TextEntry::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y-m-d H:i'), // 使用24小時制格式
            ]);
    }
}
