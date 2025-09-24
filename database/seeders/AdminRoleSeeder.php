<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * 執行資料填充
     */
    public function run(): void
    {
        // 建立管理員角色（如果不存在則建立）
        Role::firstOrCreate(
            ['name' => '管理員'],
            [
                'description' => '系統管理員，擁有所有權限',
                'is_active' => true,
                'requires_campus' => false,
                'permissions' => [
                // 校區管理權限
                'campus.view',
                'campus.create',
                'campus.edit',
                'campus.delete',

                // 課程管理權限
                'course.view',
                'course.create',
                'course.edit',
                'course.delete',

                // 學生管理權限
                'student.view',
                'student.create',
                'student.edit',
                'student.delete',

                // 出勤管理權限
                'attendance.view',
                'attendance.create',
                'attendance.edit',
                'attendance.delete',

                // 設備管理權限
                'equipment.view',
                'equipment.create',
                'equipment.edit',
                'equipment.delete',

                // 財務管理權限
                'finance.view',
                'finance.create',
                'finance.edit',
                'finance.delete',
                'finance.report',

                // 行事曆管理權限
                'view_calendar',
                'create_events',
                'update_events',
                'delete_events',
                'create_courses',
                'update_courses',
                'delete_courses',
                'manage_campus_events',
                'manage_campus_courses',

                // 系統管理權限
                'user.manage',
                'role.manage',
                'system.setting',
                'audit.log',
                ],
            ]
        );

        $this->command->info('管理員角色已建立完成！');
    }
}
