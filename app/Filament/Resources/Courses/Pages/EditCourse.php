<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 啟用/停用開關
            Action::make('toggle_status')
                ->label(fn () => $this->record->is_active ? '停用課程' : '啟用課程')
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                ->color(fn () => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn () => $this->record->is_active ? '停用課程' : '啟用課程')
                ->modalDescription(fn () => $this->record->is_active
                    ? '確定要停用此課程嗎？停用後課程將不會在選項中顯示。'
                    : '確定要啟用此課程嗎？啟用後課程將可以在選項中選擇。')
                ->action(function () {
                    $this->record->update(['is_active' => !$this->record->is_active]);
                    $this->refreshFormData(['is_active']);
                }),
            DeleteAction::make(),
        ];
    }

    // 隱藏關聯管理器
    public function getRelationManagers(): array
    {
        return [];
    }

    // 編輯保存後智能跳轉
    protected function getRedirectUrl(): string
    {
        // 檢查來源頁面，決定跳轉目標
        $previousUrl = url()->previous();

        // 如果來自 CourseDashboard 頁面，跳轉回去
        if (str_contains($previousUrl, "/admin/courses/{$this->getRecord()->id}")
            && !str_contains($previousUrl, '/edit')
            && !str_contains($previousUrl, '/details')) {
            return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
        }

        // 預設跳轉到詳情頁面
        return $this->getResource()::getUrl('details', ['record' => $this->getRecord()]);
    }
}
