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
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use Illuminate\Support\Facades\Lang;

class CampusUsers extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = '人員管理';

    public function table(Table $table): Table
    {
        // 引用共享 UsersTable，確保表格與全域資源CRUD一致
        return UsersTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->where('campus_id', $this->getOwnerRecord()->id))
            ->headerActions([
                CreateAction::make()
                    ->label(__('fields.add'))
                    ->modalHeading('建立用戶')
                    ->form(fn (Schema $form) => UserForm::configure($form))
                    ->fillForm([
                        'campus_id' => $this->getOwnerRecord()->id,
                    ])
                    ->action(function (array $data): void {
                        $data['campus_id'] = $this->getOwnerRecord()->id;
                        $this->getOwnerRecord()->users()->create($data);
                    }),
            ]);
    }
}
