<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolEvent;
use App\Models\Campus;
use Carbon\Carbon;

class NationalHolidaySeeder extends Seeder
{
    /**
     * 2025-2026年國定假日和補假資料
     */
    private array $holidays = [
        // 2025年
        ['date' => '2025-01-01', 'name' => '中華民國開國紀念日', 'type' => 'national_holiday'],
        ['date' => '2025-01-27', 'name' => '小年夜', 'type' => 'national_holiday'],
        ['date' => '2025-01-28', 'name' => '農曆除夕', 'type' => 'national_holiday'],
        ['date' => '2025-01-29', 'name' => '春節', 'type' => 'national_holiday'],
        ['date' => '2025-01-30', 'name' => '春節', 'type' => 'national_holiday'],
        ['date' => '2025-01-31', 'name' => '春節', 'type' => 'national_holiday'],
        ['date' => '2025-02-28', 'name' => '和平紀念日', 'type' => 'national_holiday'],
        ['date' => '2025-04-03', 'name' => '補假', 'type' => 'national_holiday'],
        ['date' => '2025-04-04', 'name' => '兒童節及民族掃墓節', 'type' => 'national_holiday'],
        ['date' => '2025-05-01', 'name' => '勞動節', 'type' => 'national_holiday'],
        ['date' => '2025-05-30', 'name' => '補假', 'type' => 'national_holiday'],
        ['date' => '2025-05-31', 'name' => '端午節', 'type' => 'national_holiday'],
        ['date' => '2025-09-28', 'name' => '教師節', 'type' => 'national_holiday'],
        ['date' => '2025-09-29', 'name' => '補假', 'type' => 'national_holiday'],
        ['date' => '2025-10-06', 'name' => '中秋節', 'type' => 'national_holiday'],
        ['date' => '2025-10-10', 'name' => '國慶日', 'type' => 'national_holiday'],
        ['date' => '2025-10-24', 'name' => '光復節補假', 'type' => 'national_holiday'],
        ['date' => '2025-10-25', 'name' => '光復節', 'type' => 'national_holiday'],
        ['date' => '2025-12-25', 'name' => '行憲紀念日', 'type' => 'national_holiday'],

        // 2026年
        ['date' => '2026-01-01', 'name' => '中華民國開國紀念日', 'type' => 'national_holiday'],
        ['date' => '2026-02-16', 'name' => '除夕', 'type' => 'national_holiday'],
        ['date' => '2026-02-17', 'name' => '春節', 'type' => 'national_holiday'],
        ['date' => '2026-02-18', 'name' => '春節', 'type' => 'national_holiday'],
        ['date' => '2026-02-19', 'name' => '春節', 'type' => 'national_holiday'],
        ['date' => '2026-02-20', 'name' => '春節補假', 'type' => 'national_holiday'],
        ['date' => '2026-02-27', 'name' => '和平紀念日補假', 'type' => 'national_holiday'],
        ['date' => '2026-02-28', 'name' => '和平紀念日', 'type' => 'national_holiday'],
        ['date' => '2026-04-03', 'name' => '補假', 'type' => 'national_holiday'],
        ['date' => '2026-04-04', 'name' => '兒童節及民族掃墓節', 'type' => 'national_holiday'],
        ['date' => '2026-04-05', 'name' => '清明節補假', 'type' => 'national_holiday'],
        ['date' => '2026-04-06', 'name' => '清明節', 'type' => 'national_holiday'],
        ['date' => '2026-05-01', 'name' => '勞動節', 'type' => 'national_holiday'],
        ['date' => '2026-06-19', 'name' => '端午節', 'type' => 'national_holiday'],
        ['date' => '2026-09-25', 'name' => '中秋節', 'type' => 'national_holiday'],
        ['date' => '2026-09-28', 'name' => '教師節', 'type' => 'national_holiday'],
        ['date' => '2026-10-09', 'name' => '國慶日補假', 'type' => 'national_holiday'],
        ['date' => '2026-10-10', 'name' => '國慶日', 'type' => 'national_holiday'],
        ['date' => '2026-10-25', 'name' => '光復節', 'type' => 'national_holiday'],
        ['date' => '2026-10-26', 'name' => '光復節補假', 'type' => 'national_holiday'],
        ['date' => '2026-12-25', 'name' => '行憲紀念日', 'type' => 'national_holiday'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('開始建立國定假日事件...');

        foreach ($this->holidays as $holiday) {
            $date = Carbon::parse($holiday['date']);

            // 建立全域國定假日事件（不綁定特定校區）
            SchoolEvent::create([
                'title' => $holiday['name'],
                'description' => "{$holiday['name']} - 全國各機關學校放假一日",
                'start_time' => $date->copy()->startOfDay(),
                'end_time' => $date->copy()->endOfDay(),
                'category' => $holiday['type'],
                'campus_id' => null, // 設為 null 表示全域事件
                'created_by' => 1, // 假設管理員 ID 為 1
                'status' => 'active',
                'location' => '',
            ]);

            $this->command->info("已建立全域國定假日事件：{$holiday['name']} ({$holiday['date']})");
        }

        $this->command->info("國定假日事件建立完成！總共建立了 " . count($this->holidays) . " 個全域事件。");
    }
}
