<?php

namespace App\Filament\Resources\Campuses\Relations;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Schema;
use App\Filament\Resources\Courses\Schemas\CourseForm;
use App\Filament\Resources\Courses\Tables\CoursesTable;

class CampusCourses extends RelationManager
{
    protected static string $relationship = 'courses';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = '課程管理';

    public function table(Table $table): Table
    {
        // 引用共享 CoursesTable，確保表格與全域資源CRUD一致
        return CoursesTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->where('campus_id', $this->getOwnerRecord()->id))
            ->headerActions([
                CreateAction::make()
                    ->label(__('fields.add'))
                    ->modalHeading('建立課程')
                    ->form(fn (Schema $form) => CourseForm::configure($form)
                        ->components(array_filter(
                            CourseForm::configure($form)->getComponents(),
                            fn ($component) => $component->getName() !== 'campus_id'
                        ))
                    )
                    ->fillForm([
                        'campus_id' => $this->getOwnerRecord()->id,
                        'start_time' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                        'end_time' => \Carbon\Carbon::now()->addHour()->format('Y-m-d H:i:s'),
                        'student_count' => 0,
                        'is_active' => true,
                    ])
                    ->action(function (array $data): void {
                        $data['campus_id'] = $this->getOwnerRecord()->id;
                        $this->getOwnerRecord()->courses()->create($data);
                    }),
            ]);
    }
}
