<?php

namespace App\Console\Commands;

use App\Models\SchoolEvent;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Equipment;
use App\Models\Finance;
use App\Models\Role;
use App\Models\SystemSetting;
use Illuminate\Console\Command;

class UpdateAllSortOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sort:update-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新所有模組的排序值';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('開始更新所有模組的排序值...');

        // 更新校務事件
        $this->updateSortOrder(SchoolEvent::class, '校務事件');

        // 更新用戶
        $this->updateSortOrder(User::class, '用戶');

        // 更新出勤
        $this->updateSortOrder(Attendance::class, '出勤');

        // 更新設備
        $this->updateSortOrder(Equipment::class, '設備');

        // 更新財務
        $this->updateSortOrder(Finance::class, '財務');

        // 更新角色
        $this->updateSortOrder(Role::class, '角色');

        // 更新系統設定
        $this->updateSortOrder(SystemSetting::class, '系統設定');

        $this->info('所有模組的排序值更新完成！');
    }

    private function updateSortOrder($model, $name)
    {
        $records = $model::orderBy('id')->get();

        foreach ($records as $index => $record) {
            $record->update(['sort_order' => $index + 1]);
        }

        $this->info("{$name}：更新了 {$records->count()} 筆記錄");
    }
}
