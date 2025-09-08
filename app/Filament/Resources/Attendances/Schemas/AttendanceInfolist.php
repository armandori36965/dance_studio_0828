<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AttendanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 學員姓名
                TextEntry::make('user.name')
                    ->label('學員姓名'),

                // 課程名稱
                TextEntry::make('course.name')
                    ->label('課程名稱'),

                // 上課日期
                TextEntry::make('date')
                    ->label('上課日期')
                    ->date('Y-m-d'),

                // 簽到時間
                TextEntry::make('check_in_time')
                    ->label('簽到時間')
                    ->time('H:i'),

                // 簽退時間
                TextEntry::make('check_out_time')
                    ->label('簽退時間')
                    ->time('H:i'),

                // 出勤狀態
                TextEntry::make('status')
                    ->label('出勤狀態')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => '出席',
                        'absent' => '缺席',
                        'late' => '遲到',
                        'excused' => '請假',
                        default => $state,
                    }),

                // 備註
                TextEntry::make('notes')
                    ->label('備註'),

                // 建立時間
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i'),

                // 更新時間
                TextEntry::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y-m-d H:i'),
            ]);
    }
}
