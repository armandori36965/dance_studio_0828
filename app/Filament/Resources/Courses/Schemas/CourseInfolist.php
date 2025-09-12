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
                    ->label('課程價格')
                    ->money('TWD'),
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
                IconEntry::make('is_active')
                    ->label('啟用狀態')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label('建立時間')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('更新時間')
                    ->dateTime(),
            ]);
    }
}
