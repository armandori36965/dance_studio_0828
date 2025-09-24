<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    // 覆寫表單方法，返回空的表單配置
    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    // 禁用表單編輯功能
    protected function hasUnsavedChangesAlert(): bool
    {
        return false;
    }

    // 隱藏關聯管理器
    public function getRelationManagers(): array
    {
        return [];
    }
}
