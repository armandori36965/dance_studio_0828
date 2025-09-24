<?php

namespace Database\Seeders;

use App\Models\SchoolEvent;
use App\Models\Campus;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GuoanSchoolEventSeeder extends Seeder
{
    /**
     * 執行資料填充 - 國安國小校務事件
     */
    public function run(): void
    {
        $this->command->info('開始建立國安國小校務事件...');

        // 取得國安國小校區
        $guoanCampus = Campus::where('name', '國安國小')->first();

        if (!$guoanCampus) {
            $this->command->error('找不到國安國小校區，請先建立校區資料！');
            return;
        }

        // 第一學期校務事件
        $this->createFirstSemesterEvents($guoanCampus->id);

        // 第二學期校務事件
        $this->createSecondSemesterEvents($guoanCampus->id);

        $this->command->info('國安國小校務事件建立完成！');
    }

    /**
     * 建立第一學期校務事件
     */
    private function createFirstSemesterEvents(int $campusId): void
    {
        $firstSemesterEvents = [
            // 114學年度第1學期
            ['date' => '2025-08-01', 'name' => '114學年度第1學期開始', 'type' => 'school'],
            ['date' => '2025-08-16', 'name' => '小一新生家長座談會', 'type' => 'school'],
            ['date' => '2025-08-29', 'name' => '返校日', 'type' => 'school'],
            ['date' => '2025-09-01', 'name' => '全校開學日正式上課', 'type' => 'school'],
            ['date' => '2025-09-10', 'name' => '班級學生家長會', 'type' => 'school'],
            ['date' => '2025-09-12', 'name' => '防震防災演練', 'type' => 'school'],
            ['date' => '2025-09-19', 'name' => '家長代表大會', 'type' => 'school'],
            ['date' => '2025-09-28', 'name' => '教師節慶祝活動', 'type' => 'school'],
            ['date' => '2025-10-22', 'name' => '家長會授證典禮', 'type' => 'school'],
            ['date' => '2025-10-25', 'name' => '臺灣光復節慶祝活動', 'type' => 'school'],
            ['date' => '2025-11-04', 'name' => '第一次成績評量', 'type' => 'school', 'end_date' => '2025-11-05'],
            ['date' => '2025-12-05', 'name' => '28週年校慶活動暨運動會', 'type' => 'school'],
            ['date' => '2025-12-30', 'name' => '歲末感恩活動', 'type' => 'school'],
            ['date' => '2026-01-15', 'name' => '第二次成績評量', 'type' => 'school', 'end_date' => '2026-01-16'],
            ['date' => '2026-01-20', 'name' => '第1學期休業式正常上課', 'type' => 'school'],
            ['date' => '2026-01-21', 'name' => '補上第2學期2/11-2/13課', 'type' => 'school', 'end_date' => '2026-01-23'],
        ];

        $this->createEvents($firstSemesterEvents, $campusId, '第一學期');
    }

    /**
     * 建立第二學期校務事件
     */
    private function createSecondSemesterEvents(int $campusId): void
    {
        $secondSemesterEvents = [
            // 114學年度第2學期
            ['date' => '2026-02-01', 'name' => '114學年度第2學期開始', 'type' => 'school'],
            ['date' => '2026-02-14', 'name' => '全校上課日', 'type' => 'school'],
            ['date' => '2026-03-04', 'name' => '美感作業展', 'type' => 'school', 'end_date' => '2026-03-06'],
            ['date' => '2026-03-06', 'name' => '防震防災演練', 'type' => 'school'],
            ['date' => '2026-03-27', 'name' => '兒童節校內表揚', 'type' => 'school'],
            ['date' => '2026-04-02', 'name' => '兒童節校內活動', 'type' => 'school'],
            ['date' => '2026-04-09', 'name' => '小一新生報到', 'type' => 'school', 'end_date' => '2026-04-11'],
            ['date' => '2026-04-23', 'name' => '第一次成績評量', 'type' => 'school', 'end_date' => '2026-04-24'],
            ['date' => '2026-05-08', 'name' => '國安藝術祭', 'type' => 'school'],
            ['date' => '2026-06-03', 'name' => '畢業生成績評量', 'type' => 'school', 'end_date' => '2026-06-04'],
            ['date' => '2026-06-11', 'name' => '畢業典禮', 'type' => 'school'],
            ['date' => '2026-06-12', 'name' => '幼兒園畢業典禮', 'type' => 'school'],
            ['date' => '2026-06-23', 'name' => '第二次成績評量', 'type' => 'school', 'end_date' => '2026-06-24'],
            ['date' => '2026-06-30', 'name' => '休業式', 'type' => 'school'],
            ['date' => '2026-07-01', 'name' => '暑假開始', 'type' => 'school'],
        ];

        $this->createEvents($secondSemesterEvents, $campusId, '第二學期');
    }

    /**
     * 建立校務事件
     */
    private function createEvents(array $events, int $campusId, string $semester): void
    {
        foreach ($events as $event) {
            $startDate = Carbon::parse($event['date']);
            $endDate = isset($event['end_date']) ? Carbon::parse($event['end_date']) : $startDate;

            SchoolEvent::create([
                'description' => "{$event['name']} - {$semester}校務活動",
                'start_time' => $startDate->copy()->startOfDay(),
                'end_time' => $endDate->copy()->endOfDay(),
                'category' => $event['type'],
                'campus_id' => $campusId,
                'created_by' => 1, // 假設管理員 ID 為 1
                'status' => 'active',
                'location' => '國安國小',
            ]);

            $this->command->info("已建立{$semester}校務事件：{$event['name']} ({$event['date']})");
        }
    }

}
