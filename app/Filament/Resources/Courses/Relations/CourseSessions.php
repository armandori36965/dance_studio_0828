<?php

namespace App\Filament\Resources\Courses\Relations;

use App\Models\Course;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema as FilamentSchema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CourseSessions extends RelationManager
{
    protected static string $relationship = 'sessions';

    protected static ?string $title = '課程排定日期';

    protected static ?string $model = \App\Models\CourseSession::class;

    protected static ?string $modelLabel = '排定日期';

    protected static ?string $pluralModelLabel = '排定日期';

    public function form(FilamentSchema $schema): FilamentSchema
    {
        return $schema
            ->schema([
                DateTimePicker::make('start_time')
                    ->label('開始時間')
                    ->required()
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false),

                DateTimePicker::make('end_time')
                    ->label('結束時間')
                    ->required()
                    ->seconds(false)
                    ->firstDayOfWeek(1)
                    ->locale('zh_TW')
                    ->displayFormat('Y-m-d H:i')
                    ->format('Y-m-d H:i:s')
                    ->native(false)
                    ->after('start_time')
                    ->rules(['after:start_time']),

                Select::make('teacher_id')
                    ->label('授課老師')
                    ->relationship('teacher', 'name',
                        modifyQueryUsing: fn ($query) => $query->canTeach()->orderBy('name', 'asc')
                    )
                    ->searchable()
                    ->preload(),

                Select::make('status')
                    ->label('狀態')
                    ->options([
                        'scheduled' => '已排定',
                        'completed' => '已完成',
                        'cancelled' => '已取消',
                    ])
                    ->default('scheduled')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->select([
                    'id', 'course_id', 'session_number', 'start_time',
                    'end_time', 'status', 'created_at', 'sort_order', 'teacher_id'
                ]) // 只查詢需要的欄位
                ->with('teacher:id,name') // 預載老師資料
                ->orderBy('sort_order', 'asc') // 預設排序，利用索引
            )
            ->columns([
                TextColumn::make('start_time')
                    ->label('開始時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('end_time')
                    ->label('結束時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('teacher.name')
                    ->label('授課老師')
                    ->sortable()
                    ->searchable()
                    ->default('未指定'),

                TextColumn::make('status')
                    ->label('狀態')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => '已排定',
                        'completed' => '已完成',
                        'cancelled' => '已取消',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('session_number')
                    ->label('堂數')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i') // 使用24小時制格式
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchable([
                'session_number',
                'status',
                'teacher.name',
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('狀態')
                    ->options([
                        'scheduled' => '已排定',
                        'completed' => '已完成',
                        'cancelled' => '已取消',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('新增課程')
                    ->modalHeading('建立課程')
                    ->mutateFormDataUsing(function (array $data): array {
                        // 自動設定堂數為下一個編號
                        $course = $this->getOwnerRecord();
                        $maxSession = $course->sessions()->max('session_number') ?? 0;
                        $data['session_number'] = $maxSession + 1;
                        $data['sort_order'] = $maxSession + 1;

                        // 如果沒有指定老師，使用課程預設老師
                        if (empty($data['teacher_id']) && $course->teacher_id) {
                            $data['teacher_id'] = $course->teacher_id;
                        }

                        return $data;
                    }),
                Action::make('reorder')
                    ->label('重新排序堂數')
                    ->icon('heroicon-o-arrows-up-down')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('重新排序堂數')
                    ->modalDescription('這將根據開始時間重新編排所有堂數，確定要繼續嗎？')
                    ->action(function () {
                        $this->reorderSessionNumbers();
                        \Filament\Notifications\Notification::make()
                            ->title('堂數重新排序完成')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label('編輯'),
                DeleteAction::make()
                    ->label('刪除')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('刪除所選的項目'),
                ]),
            ])
            ->extremePaginationLinks() // 顯示第一頁和最後一頁按鈕
            ->paginated([10, 20, 30, 50]) // 調整分頁選項，提高預設值
            ->defaultPaginationPageOption(20) // 提高預設每頁顯示筆數
            ->paginationPageOptions([10, 20, 30, 50]) // 確保分頁選項正確設定
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->deferLoading(); // 延遲載入，提高初始頁面載入速度
    }

    // 啟用批量選擇功能
    public function isReadOnly(): bool
    {
        return false;
    }

    // 重新排序堂數 - 優化版本
    protected function reorderSessionNumbers(): void
    {
        $course = $this->getOwnerRecord();

        // 先取得按順序排列的ID列表
        $sessionIds = $course->sessions()
            ->orderBy('start_time', 'asc')
            ->orderBy('sort_order', 'asc')
            ->pluck('id')
            ->toArray();

        // 使用批量更新，避免逐一更新
        foreach ($sessionIds as $index => $sessionId) {
            DB::table('course_sessions')
                ->where('id', $sessionId)
                ->update([
                    'session_number' => $index + 1,
                    'sort_order' => $index + 1,
                ]);
        }

        // 或者考慮延遲處理避免影響用戶體驗
        // dispatch(new ReorderCourseSessionsJob($course->id))->onQueue('low');
    }
}
