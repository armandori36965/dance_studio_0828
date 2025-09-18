<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 載入現有的課程關聯，明確指定要選擇的欄位
        $allCourseIds = $this->record->courses()->pluck('courses.id')->toArray();

        // 根據校區和補習班分離課程
        $campusCourseIds = [];
        $cramSchoolCourseIds = [];

        foreach ($allCourseIds as $courseId) {
            $course = \App\Models\Course::find($courseId);
            if ($course) {
                if ($course->campus->type === 'school') {
                    $campusCourseIds[] = $courseId;
                } elseif ($course->campus->type === 'cram_school') {
                    $cramSchoolCourseIds[] = $courseId;
                }
            }
        }

        $data['course_ids'] = $campusCourseIds;
        $data['cram_school_course_ids'] = $cramSchoolCourseIds;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 處理課程關聯
        $courseIds = $data['course_ids'] ?? [];
        $cramSchoolCourseIds = $data['cram_school_course_ids'] ?? [];
        unset($data['course_ids'], $data['cram_school_course_ids']);

        // 合併所有課程ID
        $allCourseIds = array_merge($courseIds, $cramSchoolCourseIds);

        // 保存用戶後再處理課程關聯
        $this->courseIds = $allCourseIds;

        return $data;
    }

    protected function afterSave(): void
    {
        // 保存課程關聯
        if (isset($this->courseIds)) {
            $this->record->courses()->sync($this->courseIds);
        }
    }

    private array $courseIds = [];
}
