<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 執行遷移
     */
    public function up(): void
    {
        // 如果表不存在，創建表
        if (!Schema::hasTable('school_events')) {
            Schema::create('school_events', function (Blueprint $table) {
                $table->id();
                $table->string('title')->comment('事件標題');
                $table->text('description')->nullable()->comment('事件描述');
                $table->datetime('start_time')->comment('開始時間');
                $table->datetime('end_time')->nullable()->comment('結束時間');
                $table->string('location')->nullable()->comment('地點');
                $table->string('category', 50)->nullable()->comment('事件類型');
                $table->string('status', 20)->default('active')->comment('狀態');
                $table->foreignId('campus_id')->nullable()->constrained()->onDelete('set null')->comment('所屬校區');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('創建者');
                $table->timestamps();
            });
        } else {
            // 如果表存在，添加缺失的欄位
            Schema::table('school_events', function (Blueprint $table) {
                // 檢查並添加 category 欄位
                if (!Schema::hasColumn('school_events', 'category')) {
                    $table->string('category', 50)->nullable()->comment('事件類型');
                }

                // 檢查並添加 campus_id 欄位
                if (!Schema::hasColumn('school_events', 'campus_id')) {
                    $table->foreignId('campus_id')->nullable()->constrained()->onDelete('set null')->comment('所屬校區');
                }

                // 檢查並添加 created_by 欄位
                if (!Schema::hasColumn('school_events', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('創建者');
                }

                // 檢查並修復欄位名稱
                if (Schema::hasColumn('school_events', 'start_date') && !Schema::hasColumn('school_events', 'start_time')) {
                    $table->renameColumn('start_date', 'start_time');
                }

                if (Schema::hasColumn('school_events', 'end_date') && !Schema::hasColumn('school_events', 'end_time')) {
                    $table->renameColumn('end_date', 'end_time');
                }
            });
        }

        // 插入校區 4 的樣本資料（如果不存在）
        if (DB::table('school_events')->where('campus_id', 4)->count() === 0) {
            DB::table('school_events')->insert([
                [
                    'title' => '校區 4 舞蹈課程開課',
                    'description' => '為校區 4 的學員開設基礎舞蹈課程',
                    'start_time' => now()->addDays(7),
                    'end_time' => now()->addDays(7)->addHours(2),
                    'location' => '校區 4 舞蹈教室 A',
                    'category' => 'course',
                    'status' => 'active',
                    'campus_id' => 4,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => '校區 4 表演活動',
                    'description' => '校區 4 學員成果展示表演',
                    'start_time' => now()->addDays(14),
                    'end_time' => now()->addDays(14)->addHours(3),
                    'location' => '校區 4 表演廳',
                    'category' => 'performance',
                    'status' => 'active',
                    'campus_id' => 4,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => '校區 4 教師會議',
                    'description' => '討論校區 4 的教學計劃和學員進度',
                    'start_time' => now()->addDays(2),
                    'end_time' => now()->addDays(2)->addHours(1),
                    'location' => '校區 4 會議室',
                    'category' => 'meeting',
                    'status' => 'active',
                    'campus_id' => 4,
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        // 刪除樣本資料
        DB::table('school_events')
            ->where('campus_id', 4)
            ->whereIn('title', [
                '校區 4 舞蹈課程開課',
                '校區 4 表演活動',
                '校區 4 教師會議'
            ])
            ->delete();

        // 不刪除表，因為可能被其他資料使用
    }
};
