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
use App\Filament\Resources\Finances\Schemas\FinanceForm;
use App\Filament\Resources\Finances\Tables\FinancesTable;
use Illuminate\Support\Facades\Lang;

class CampusFinances extends RelationManager
{
    protected static string $relationship = 'finances';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = '財務管理';

    public function table(Table $table): Table
    {
        // 引用共享 FinancesTable，確保表格與全域資源CRUD一致
        return FinancesTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->where('campus_id', $this->getOwnerRecord()->id))
            ->headerActions([
                CreateAction::make()
                    ->label(__('fields.add'))
                    ->modalHeading('建立財務記錄')
                    ->form(fn (Schema $form) => FinanceForm::configure($form))
                    ->fillForm([
                        'campus_id' => $this->getOwnerRecord()->id,
                    ])
                    ->action(function (array $data): void {
                        $data['campus_id'] = $this->getOwnerRecord()->id;
                        $this->getOwnerRecord()->finances()->create($data);
                    }),
            ]);
    }
}
