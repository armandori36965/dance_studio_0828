<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Courses\Relations\CourseSessions;
use App\Filament\Resources\Courses\Schemas\CourseInfolist;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Actions\Action;

class CourseDashboard extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected static ?string $title = ' ';

    // 設定頁面標題
    public function getTitle(): string
    {
        return $this->getRecord()->name . ' - 課程排定';
    }

    // 設定頁面描述
    public function getDescription(): string
    {
        return '管理課程的排定日期和相關資訊';
    }

    // 設定右上角操作按鈕
    protected function getHeaderActions(): array
    {
        return [
            // 詳情按鈕 - 導航到 ViewCourse
            Action::make('details')
                ->label('詳情')
                ->icon('heroicon-o-information-circle')
                ->url(fn () => CourseResource::getUrl('details', ['record' => $this->getRecord()]))
                ->openUrlInNewTab(false),
        ];
    }

    // 顯示課程基本資訊
    public function infolist(Schema $schema): Schema
    {
        return CourseInfolist::configure($schema);
    }

    // 移除表單顯示
    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    // 隱藏保存和取消按鈕
    protected function getFormActions(): array
    {
        return [];
    }

    // 設定關聯管理器（課程排定）
    public function getRelationManagers(): array
    {
        return [
            CourseSessions::class,
        ];
    }
}
