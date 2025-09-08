<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Course;
use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckData extends Command
{
    /**
     * 命令名稱
     */
    protected $signature = 'check:data';

    /**
     * 命令描述
     */
    protected $description = '檢查資料庫中的資料';

    /**
     * 執行命令
     */
    public function handle()
    {
        $this->info('=== 資料庫資料檢查 ===');

        // 檢查資料表
        $this->info('資料表列表：');
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            $this->line("- {$tableName}");
        }

        $this->newLine();

        // 檢查校區
        $this->info('校區資料：');
        $campuses = Campus::all();
        $this->info("總數量：{$campuses->count()} 個");
        foreach ($campuses as $campus) {
            $this->line("- {$campus->name} ({$campus->address})");
        }

        $this->newLine();

        // 檢查課程
        $this->info('課程資料：');
        $courses = Course::all();
        $this->info("總數量：{$courses->count()} 個");
        foreach ($courses as $course) {
            $this->line("- {$course->name} (NT$ {$course->price})");
        }

        $this->newLine();

        // 檢查用戶
        $this->info('用戶資料：');
        $users = User::all();
        $this->info("總數量：{$users->count()} 個");
        foreach ($users as $user) {
            $roleName = '無角色';
            if (DB::getSchemaBuilder()->hasTable('roles')) {
                try {
                    $role = $user->role;
                    $roleName = $role ? $role->name : '無角色';
                } catch (\Exception $e) {
                    $roleName = '角色表錯誤';
                }
            }
            $this->line("- {$user->name} ({$user->email}) - 角色：{$roleName}");
        }

        $this->newLine();

        // 檢查角色
        if (DB::getSchemaBuilder()->hasTable('roles')) {
            $this->info('角色資料：');
            $roles = Role::all();
            $this->info("總數量：{$roles->count()} 個");
            foreach ($roles as $role) {
                $this->line("- {$role->name} (權限數量：" . count($role->permissions ?? []) . ")");
            }
        } else {
            $this->info('角色資料：角色表不存在');
        }
    }
}
