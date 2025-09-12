<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearPerformanceCache extends Command
{
    /**
     * 命令名稱和簽名
     */
    protected $signature = 'cache:performance-clear';

    /**
     * 命令描述
     */
    protected $description = '清理效能相關的快取';

    /**
     * 執行命令
     */
    public function handle()
    {
        // 清理 Widget 快取
        Cache::forget('global_events_calendar');
        Cache::forget('showcase_stats');

        // 清理校區統計快取
        $campusIds = \App\Models\Campus::pluck('id');
        foreach ($campusIds as $campusId) {
            Cache::forget("campus_stats_{$campusId}");
        }

        $this->info('效能快取已清理完成！');

        return Command::SUCCESS;
    }
}

