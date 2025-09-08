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
use App\Filament\Resources\Equipment\Schemas\EquipmentForm;
use App\Filament\Resources\Equipment\Tables\EquipmentTable;
use Illuminate\Support\Facades\Lang;

class CampusEquipment extends RelationManager
{
    protected static string $relationship = 'equipment';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = '設備管理';

    public function table(Table $table): Table
    {
        // 引用共享 EquipmentTable，確保表格與全域資源CRUD一致
        return EquipmentTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->where('campus_id', $this->getOwnerRecord()->id))
            ->headerActions([
                CreateAction::make()
                    ->label(__('fields.add'))
                    ->modalHeading('建立設備')
                    ->form(fn (Schema $form) => EquipmentForm::configure($form))
                    ->fillForm([
                        'campus_id' => $this->getOwnerRecord()->id,
                    ])
                    ->action(function (array $data): void {
                        $data['campus_id'] = $this->getOwnerRecord()->id;
                        $this->getOwnerRecord()->equipment()->create($data);
                    }),
            ]);
    }
}
