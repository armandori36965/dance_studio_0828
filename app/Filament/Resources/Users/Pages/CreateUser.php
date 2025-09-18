<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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

    protected function afterCreate(): void
    {
        // 保存課程關聯
        if (!empty($this->courseIds)) {
            $this->record->courses()->sync($this->courseIds);
        }
    }

    private array $courseIds = [];
}
