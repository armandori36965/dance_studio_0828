<?php

namespace Database\Seeders;

use App\Models\SchoolEvent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolEventSeeder extends Seeder
{
    /**
     * 執行資料填充
     */
    public function run(): void
    {
        // 取得管理員用戶
        $adminUser = User::where('email', 'admin@dance.com')->first();

        if (!$adminUser) {
            $this->command->error('管理員用戶不存在，請先執行 AdminUserSeeder！');
            return;
        }

        // 建立校務活動
        SchoolEvent::create([
            'title' => '新生入學說明會',
            'description' => '為新生家長舉辦的入學說明會，介紹課程安排和注意事項',
            'start_date' => now()->addDays(7)->setTime(14, 0),
            'end_date' => now()->addDays(7)->setTime(16, 0),
            'location' => '主校區大廳',
            'status' => 'active',
            'created_by' => $adminUser->id,
        ]);

        SchoolEvent::create([
            'title' => '舞蹈比賽準備課程',
            'description' => '為參加舞蹈比賽的學生開設的專項準備課程',
            'start_date' => now()->addDays(14)->setTime(18, 0),
            'end_date' => now()->addDays(14)->setTime(20, 0),
            'location' => '舞蹈教室A',
            'status' => 'active',
            'created_by' => $adminUser->id,
        ]);

        SchoolEvent::create([
            'title' => '家長觀摩日',
            'description' => '邀請家長觀摩學生的舞蹈課程，了解學習進度',
            'start_date' => now()->addDays(21)->setTime(15, 0),
            'end_date' => now()->addDays(21)->setTime(17, 0),
            'location' => '所有舞蹈教室',
            'status' => 'active',
            'created_by' => $adminUser->id,
        ]);

        $this->command->info('校務活動已建立完成！');
    }
}
