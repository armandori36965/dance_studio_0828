<?php

namespace App\Filament\Resources\Campuses\Relations;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Schema;
use App\Filament\Resources\SchoolEvents\Schemas\SchoolEventForm;
use App\Filament\Resources\SchoolEvents\Tables\SchoolEventsTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\SchoolEvent;
use App\Filament\Resources\SchoolEvents\SchoolEventResource;


class CampusSchoolEvents extends RelationManager
{
    protected static string $relationship = 'schoolEvents';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $title = '校務事件';



    public static function getResource(): string
    {
        return SchoolEventResource::class;
    }

    public function canCreate(): bool
    {
        return true;
    }

    public function canEdit($record): bool
    {
        return true;
    }

    public function canDelete($record): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        // 引用共享 SchoolEventsTable，確保表格與全域資源CRUD一致（欄位、篩選、動作同步）
        return SchoolEventsTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->label(__('fields.add'))
                    ->modalHeading('建立校務事件')
                    ->form(fn (Schema $form) => SchoolEventForm::configure($form)
                        ->components(array_filter(
                            SchoolEventForm::configure($form)->getComponents(),
                            fn ($component) => $component->getName() !== 'campus_id'
                        ))
                    )
                    ->fillForm([
                        'campus_id' => $this->getOwnerRecord()->id,
                        'created_by' => Auth::id(),
                        'start_time' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                        'end_time' => \Carbon\Carbon::now()->addHour()->format('Y-m-d H:i:s'),
                    ])
                    ->action(function (array $data): void {
                        $data['campus_id'] = $this->getOwnerRecord()->id;
                        $data['created_by'] = Auth::id();

                        $this->getOwnerRecord()->schoolEvents()->create($data);
                    }),
            ])
            ->extremePaginationLinks() // 改善分頁顯示
            ->paginated([10, 25, 50, 100]) // 設定每頁顯示筆數選項
            ->defaultPaginationPageOption(10) // 預設每頁顯示10筆
;
    }
}
