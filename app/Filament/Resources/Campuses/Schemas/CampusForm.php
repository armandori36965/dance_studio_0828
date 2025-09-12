<?php

namespace App\Filament\Resources\Campuses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;

class CampusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 校區名稱
                TextInput::make('name')
                    ->label(__('fields.campus_name'))
                    ->required()
                    ->columnSpanFull(),

                // 聯絡資訊區塊 - 電話和郵件
                TextInput::make('phone')
                    ->label(__('fields.phone'))
                    ->tel()
                    ->default(null)
                    ->columnSpan(1),
                TextInput::make('email')
                    ->label(__('fields.email_address'))
                    ->email()
                    ->default(null)
                    ->columnSpan(1),

                // 設定區塊 - 顏色和啟用狀態
                ColorPicker::make('color')
                    ->label(__('fields.color'))
                    ->default('#3B82F6')
                    ->hex()
                    ->columnSpan(1),
                Toggle::make('is_active')
                    ->label(__('fields.is_active'))
                    ->default(true)
                    ->required()
                    ->columnSpan(1)
                    ->inline(false),

                // 地址和描述
                TextInput::make('address')
                    ->label(__('fields.address'))
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label(__('fields.description'))
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
