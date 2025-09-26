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
use App\Filament\Resources\Attendances\Schemas\AttendanceForm;
use App\Filament\Resources\Attendances\Tables\AttendancesTable;
use Illuminate\Support\Facades\Lang;

class CampusAttendances extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = '差勤管理';

    public function table(Table $table): Table
    {
        // 引用共享 AttendancesTable，確保表格與全域資源CRUD一致
        return AttendancesTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->where('campus_id', $this->getOwnerRecord()->id))
            ->headerActions([
                CreateAction::make()
                    ->label(__('fields.add'))
                    ->modalHeading('建立差勤記錄')
                    ->form(fn (Schema $form) => AttendanceForm::configure($form))
                    ->fillForm([
                        'campus_id' => $this->getOwnerRecord()->id,
                    ])
                    ->action(function (array $data): void {
                        $data['campus_id'] = $this->getOwnerRecord()->id;
                        $this->getOwnerRecord()->attendances()->create($data);
                    }),
            ]);
    }
}
