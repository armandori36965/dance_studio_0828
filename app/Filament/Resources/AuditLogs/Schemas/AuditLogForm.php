<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 用戶選擇
                Select::make('user_id')
                    ->label(__('fields.user_name'))
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->placeholder(__('actions.choose') . __('fields.user_name'))
                    ->required(),

                // 操作類型
                Select::make('action')
                    ->label(__('fields.action_type'))
                    ->options([
                        'create' => __('actions.create'),
                        'update' => __('actions.update'),
                        'delete' => __('actions.delete'),
                        'login' => __('actions.log_in'),
                        'logout' => __('actions.log_out'),
                    ])
                    ->required(),

                // 模型類型
                TextInput::make('model_type')
                    ->label(__('fields.model_type'))
                    ->required()
                    ->maxLength(255),

                // 模型ID
                TextInput::make('model_id')
                    ->label(__('fields.model_id'))
                    ->numeric()
                    ->required(),

                // 舊值
                Textarea::make('old_values')
                    ->label(__('fields.old_values'))
                    ->rows(3)
                    ->columnSpanFull(),

                // 新值
                Textarea::make('new_values')
                    ->label(__('fields.new_values'))
                    ->rows(3)
                    ->columnSpanFull(),

                // IP地址
                TextInput::make('ip_address')
                    ->label(__('fields.ip_address'))
                    ->maxLength(45),

                // 用戶代理
                Textarea::make('user_agent')
                    ->label(__('fields.user_agent'))
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}
