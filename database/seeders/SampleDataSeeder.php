<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * 執行資料填充
     */
    public function run(): void
    {
        // 建立校區資料
        $campus1 = Campus::create([
            'name' => '台北總校',
            'address' => '台北市信義區信義路五段7號',
            'phone' => '02-2345-6789',
            'email' => 'taipei@dance.com',
            'is_active' => true,
        ]);

        $campus2 = Campus::create([
            'name' => '台中分校',
            'address' => '台中市西區精誠路123號',
            'phone' => '04-2345-6789',
            'email' => 'taichung@dance.com',
            'is_active' => true,
        ]);

        $campus3 = Campus::create([
            'name' => '高雄分校',
            'address' => '高雄市前金區中正路456號',
            'phone' => '07-2345-6789',
            'email' => 'kaohsiung@dance.com',
            'is_active' => true,
        ]);

        // 建立課程資料
        Course::create([
            'name' => '芭蕾舞基礎班',
            'description' => '適合初學者的芭蕾舞基礎課程',
            'price' => 2500.00,
            'duration' => 60,
            'max_students' => 15,
            'campus_id' => $campus1->id,
            'level' => 'beginner',
            'is_active' => true,
        ]);

        Course::create([
            'name' => '現代舞進階班',
            'description' => '現代舞技巧提升課程',
            'price' => 3000.00,
            'duration' => 90,
            'max_students' => 12,
            'campus_id' => $campus1->id,
            'level' => 'intermediate',
            'is_active' => true,
        ]);

        Course::create([
            'name' => '街舞基礎班',
            'description' => '街舞入門課程',
            'price' => 2000.00,
            'duration' => 60,
            'max_students' => 20,
            'campus_id' => $campus2->id,
            'level' => 'beginner',
            'is_active' => true,
        ]);

        Course::create([
            'name' => '爵士舞進階班',
            'description' => '爵士舞技巧提升課程',
            'price' => 2800.00,
            'duration' => 75,
            'max_students' => 15,
            'campus_id' => $campus2->id,
            'level' => 'intermediate',
            'is_active' => true,
        ]);

        Course::create([
            'name' => '民族舞基礎班',
            'description' => '傳統民族舞蹈課程',
            'price' => 2200.00,
            'duration' => 60,
            'max_students' => 18,
            'campus_id' => $campus3->id,
            'level' => 'beginner',
            'is_active' => true,
        ]);

        $this->command->info('範例資料已建立完成！');
        $this->command->info('校區：3 個');
        $this->command->info('課程：5 個');
    }
}
