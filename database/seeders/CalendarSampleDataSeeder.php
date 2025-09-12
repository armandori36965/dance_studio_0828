<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolEvent;
use App\Models\Course;
use App\Models\Campus;
use Carbon\Carbon;

class CalendarSampleDataSeeder extends Seeder
{
    /**
     * 運行資料庫填充
     */
    public function run(): void
    {
        // 獲取所有活躍校區
        $campuses = Campus::where('is_active', true)->get();

        if ($campuses->isEmpty()) {
            $this->command->warn('沒有找到活躍的校區，請先運行校區 Seeder');
            return;
        }

        foreach ($campuses as $campus) {
            $this->createSchoolEvents($campus);
            $this->createCourses($campus);
        }

        $this->command->info('行事曆範例資料已創建完成！');
    }

    /**
     * 為校區創建校務活動
     */
    protected function createSchoolEvents(Campus $campus): void
    {
        $events = [
            [
                'title' => '開學典禮',
                'description' => '新學期開學典禮，歡迎所有學員',
                'category' => 'meeting',
                'location' => $campus->name . ' 大廳',
            ],
            [
                'title' => '舞蹈表演',
                'description' => '學員成果發表會',
                'category' => 'performance',
                'location' => $campus->name . ' 表演廳',
            ],
            [
                'title' => '家長座談會',
                'description' => '與家長討論學員學習狀況',
                'category' => 'meeting',
                'location' => $campus->name . ' 會議室',
            ],
        ];

        foreach ($events as $eventData) {
            $startTime = $this->getRandomDateTime('2025-09-01', '2025-11-30');
            $endTime = $startTime->copy()->addHours(2);

            SchoolEvent::create([
                'title' => $eventData['title'],
                'description' => $eventData['description'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => $eventData['location'],
                'category' => $eventData['category'],
                'status' => 'active',
                'campus_id' => $campus->id,
                'created_by' => 1,
            ]);
        }
    }

    /**
     * 為校區創建課程
     */
    protected function createCourses(Campus $campus): void
    {
        $courseTypes = [
            ['name' => '兒童芭蕾', 'level' => 'beginner', 'price' => 1200],
            ['name' => '現代舞', 'level' => 'intermediate', 'price' => 1500],
            ['name' => '民族舞', 'level' => 'advanced', 'price' => 1800],
        ];

        foreach ($courseTypes as $courseData) {
            $startTime = $this->getRandomDateTime('2025-09-01', '2025-11-30');
            $endTime = $startTime->copy()->addHours(1);

            Course::create([
                'name' => $courseData['name'],
                'description' => $courseData['name'] . '課程',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'level' => $courseData['level'],
                'price' => $courseData['price'],
                'student_count' => rand(8, 20),
                'is_active' => true,
                'campus_id' => $campus->id,
            ]);
        }
    }

    /**
     * 獲取隨機日期時間
     */
    protected function getRandomDateTime(string $startDate, string $endDate): Carbon
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $randomTimestamp = rand($start->timestamp, $end->timestamp);
        $randomDate = Carbon::createFromTimestamp($randomTimestamp);

        // 設定時間為工作時間 (9:00-18:00)
        $hour = rand(9, 17);
        $minute = rand(0, 59);

        return $randomDate->setTime($hour, $minute);
    }
}
