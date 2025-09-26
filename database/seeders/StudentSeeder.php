<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Campus;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * 執行資料填充 - 新增學生用戶
     */
    public function run(): void
    {
        $this->command->info('開始建立學生用戶...');

        // 取得學生角色
        $studentRole = Role::where('name', '學生')->first();
        if (!$studentRole) {
            $this->command->error('找不到學生角色，請先建立角色資料！');
            return;
        }

        // 取得國安國小校區
        $guoanCampus = Campus::where('name', '國安國小')->first();
        if (!$guoanCampus) {
            $this->command->error('找不到國安國小校區，請先建立校區資料！');
            return;
        }

        // 取得國-午2和國-午2翻課程
        $courses = Course::whereIn('name', ['國-午2', '國-午2翻'])->where('campus_id', $guoanCampus->id)->get();
        if ($courses->isEmpty()) {
            $this->command->error('找不到國-午2或國-午2翻課程，請先建立課程資料！');
            return;
        }

        // 學生資料
        $students = [
            ['class' => '301', 'name' => '潘姿穎'],
            ['class' => '303', 'name' => '沈子珈'],
            ['class' => '304', 'name' => '邱芊瑀'],
            ['class' => '305', 'name' => '黃琬甯'],
            ['class' => '306', 'name' => '謝苡樂'],
            ['class' => '306', 'name' => '陳卉栩'],
            ['class' => '306', 'name' => '陳君芃'],
            ['class' => '308', 'name' => '吳富華'],
            ['class' => '309', 'name' => '簡妤宸'],
            ['class' => '405', 'name' => '吳宸希'],
            ['class' => '406', 'name' => '胡睿恩'],
            ['class' => '406', 'name' => '張沛俞'],
            ['class' => '408', 'name' => '劉千慈'],
            ['class' => '411', 'name' => '柯煜廂'],
            ['class' => '411', 'name' => '張沛俞'],
            ['class' => '502', 'name' => '何妍蓁'],
            ['class' => '502', 'name' => '詹容嘉'],
            ['class' => '504', 'name' => '陳品瑜'],
            ['class' => '505', 'name' => '原淇軒'],
            ['class' => '505', 'name' => '楊媛媛'],
            ['class' => '507', 'name' => '陳亮縈'],
            ['class' => '509', 'name' => '吳楚玲'],
            ['class' => '601', 'name' => '趙芯澄'],
            ['class' => '603', 'name' => '蔡朋燕'],
            ['class' => '603', 'name' => '陳俞芮'],
            ['class' => '604', 'name' => '林羽彤'],
            ['class' => '604', 'name' => '詹采臻'],
            ['class' => '609', 'name' => '沈苡瑄'],
        ];

        foreach ($students as $studentData) {
            // 檢查是否已存在相同班級和姓名的學生
            $existingStudent = User::where('class', $studentData['class'])
                ->where('name', $studentData['name'])
                ->where('campus_id', $guoanCampus->id)
                ->first();

            if ($existingStudent) {
                $this->command->info("學生 {$studentData['class']}{$studentData['name']} 已存在，跳過");
                continue;
            }

            // 創建學生用戶
            $user = User::create([
                'name' => $studentData['name'],
                'email' => 'student' . $studentData['class'] . '_' . uniqid() . '@dance.com', // 生成唯一email
                'password' => Hash::make('password123'), // 設定預設密碼
                'role_id' => $studentRole->id,
                'campus_id' => $guoanCampus->id,
                'class' => $studentData['class'],
                'sort_order' => 0,
                'status' => 'active',
            ]);

            // 將學生加入國-午1和國-午1翻課程
            foreach ($courses as $course) {
                $user->courses()->attach($course->id, [
                    'enrolled_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $courseNames = $courses->pluck('name')->join('、');
            $this->command->info("已建立學生：{$studentData['class']}{$studentData['name']} 並加入課程：{$courseNames}");
        }

        $this->command->info('學生用戶建立完成！');
    }
}
