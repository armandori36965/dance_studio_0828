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
            ->columns(2)
            ->components([
                // 第一行：課程名稱、所屬校區
                TextEntry::make('name')
                    ->label('課程名稱')
                    ->columnSpan(1),
                TextEntry::make('campus.name')
                    ->label('所屬校區')
                    ->columnSpan(1),

                // 第二行：課程描述（全寬）
                TextEntry::make('description')
                    ->label('課程描述')
                    ->columnSpanFull(),

                // 第三行：課程等級、課程時數
                TextEntry::make('level')
                    ->label('課程等級')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beginner' => '初級',
                        'intermediate' => '中級',
                        'advanced' => '高級',
                        'competition' => '比賽隊',
                        default => $state,
                    })
                    ->columnSpan(1),
                TextEntry::make('duration')
                    ->label('課程時數')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            // 直接顯示小時，保留小數點，移除不必要的零
                            $formatted = rtrim(rtrim(number_format($state, 1), '0'), '.');
                            return $formatted . '小時';
                        }
                        return '-';
                    })
                    ->columnSpan(1),

                // 第四行：上課週期（全寬）
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
                    })
                    ->columnSpanFull(),

                // 第五行：開始時間、結束時間
                TextEntry::make('start_time')
                    ->label('開始時間')
                    ->dateTime('H:i')
                    ->columnSpan(1),
                TextEntry::make('end_time')
                    ->label('結束時間')
                    ->dateTime('H:i')
                    ->columnSpan(1),

                // 第六行：授課老師、助教老師
                TextEntry::make('teacher.name')
                    ->label('授課老師')
                    ->placeholder('未指派')
                    ->columnSpan(1),
                TextEntry::make('assistant.name')
                    ->label('助教老師')
                    ->placeholder('未指派')
                    ->columnSpan(1),

                // 第七行：總共堂數、目前學員數
                TextEntry::make('total_sessions')
                    ->label('總共堂數')
                    ->formatStateUsing(fn ($state) => $state ? $state . '堂' : '-')
                    ->columnSpan(1),
                TextEntry::make('student_count')
                    ->label('目前學員數')
                    ->getStateUsing(function ($record) {
                        // 動態計算實際報名的學生數量
                        return $record->students()->count();
                    })
                    ->columnSpan(1),

                // 第八行：計算方式、課程價格
                TextEntry::make('pricing_type')
                    ->label('計算方式')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'per_session' => '每堂課計費',
                        'per_student' => '依報名人數計費',
                        default => $state,
                    })
                    ->columnSpan(1),
                TextEntry::make('price')
                    ->label('課程價格')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0))
                    ->columnSpan(1),

                // 第九行：總價格（全寬）
                TextEntry::make('total_price')
                    ->label('總價格')
                    ->getStateUsing(fn ($record) => $record->getTotalPrice())
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0))
                    ->helperText('根據價格計算方式和實際堂數/學員數計算')
                    ->columnSpanFull(),

                // 第十行：建立時間、更新時間
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->columnSpan(1),
                TextEntry::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y-m-d H:i')
                    ->columnSpan(1),
            ]);
    }
}
