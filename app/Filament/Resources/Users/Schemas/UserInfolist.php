<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 用戶姓名
                TextEntry::make('name')
                    ->label(__('fields.user_name')),

                // 電子郵件
                TextEntry::make('email')
                    ->label(__('fields.email')),

                // 角色
                TextEntry::make('role.name')
                    ->label(__('fields.role')),

                // 校區
                TextEntry::make('campus.name')
                    ->label(__('fields.campus'))
                    ->visible(fn ($record) => $record && $record->campus),

                // 補習班
                TextEntry::make('cramSchool.name')
                    ->label('補習班')
                    ->visible(fn ($record) => $record && $record->cramSchool),

                // 班級
                TextEntry::make('class')
                    ->label('班級')
                    ->visible(fn ($record) => $record && $record->role && $record->role->name === '學生' && $record->class),

                // 緊急聯絡人姓名
                TextEntry::make('emergency_contact_name')
                    ->label('緊急聯絡人姓名')
                    ->visible(fn ($record) => $record && $record->role && $record->role->name === '學生' && $record->emergency_contact_name),

                // 緊急聯絡人電話
                TextEntry::make('emergency_contact_phone')
                    ->label('緊急聯絡人電話')
                    ->visible(fn ($record) => $record && $record->role && $record->role->name === '學生' && $record->emergency_contact_phone),

                // 學校課程
                TextEntry::make('school_courses')
                    ->label('學校課程')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->courses) {
                            return [];
                        }

                        $courses = $record->courses->load('campus');
                        return $courses->filter(function ($course) {
                            return $course->campus && $course->campus->type === 'school';
                        })->pluck('name')->toArray();
                    })
                    ->separator(', ')
                    ->visible(fn ($record) => $record && $record->role && $record->role->name === '學生'),

                // 補習班課程
                TextEntry::make('cram_school_courses')
                    ->label('補習班課程')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->courses) {
                            return [];
                        }

                        $courses = $record->courses->load('campus');
                        return $courses->filter(function ($course) {
                            return $course->campus && $course->campus->type === 'cram_school';
                        })->pluck('name')->toArray();
                    })
                    ->separator(', ')
                    ->visible(fn ($record) => $record && $record->role && $record->role->name === '學生'),


                // 建立時間
                TextEntry::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime(),

                // 更新時間
                TextEntry::make('updated_at')
                    ->label(__('fields.updated_at'))
                    ->dateTime(),
            ]);
    }
}
