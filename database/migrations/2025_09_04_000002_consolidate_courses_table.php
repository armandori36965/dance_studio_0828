<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移
     */
    public function up(): void
    {
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('課程名稱');
                $table->text('description')->nullable()->comment('課程描述');
                $table->decimal('price', 10, 2)->default(0)->comment('課程價格');
                $table->datetime('start_time')->nullable()->comment('課程開始時間');
                $table->datetime('end_time')->nullable()->comment('課程結束時間');
                $table->integer('student_count')->default(0)->comment('目前報名學員數');
                $table->foreignId('campus_id')->constrained()->onDelete('cascade')->comment('所屬校區');
                $table->string('level')->default('beginner')->comment('課程等級');
                $table->boolean('is_active')->default(true)->comment('是否啟用');
                $table->timestamps();
            });
        } else {
            Schema::table('courses', function (Blueprint $table) {
                // 移除不需要的欄位（基於原有 fix 邏輯）
                $columnsToRemove = [
                    'category', 'min_students', 'start_date', 'end_date',
                    'location', 'teacher_id', 'notes', 'deleted_at', 'duration', 'max_students'
                ];
                foreach ($columnsToRemove as $column) {
                    if (Schema::hasColumn('courses', $column)) {
                        if (in_array($column, ['teacher_id'])) {
                            $table->dropForeign([$column]);
                        }
                        $table->dropColumn($column);
                    }
                }

                // 重命名 title 為 name（如果存在）
                if (Schema::hasColumn('courses', 'title') && !Schema::hasColumn('courses', 'name')) {
                    $table->renameColumn('title', 'name');
                }

                // 添加新欄位如果不存在
                if (!Schema::hasColumn('courses', 'start_time')) {
                    $table->datetime('start_time')->nullable()->comment('課程開始時間');
                }
                if (!Schema::hasColumn('courses', 'end_time')) {
                    $table->datetime('end_time')->nullable()->comment('課程結束時間');
                }
                if (!Schema::hasColumn('courses', 'student_count')) {
                    $table->integer('student_count')->default(0)->comment('目前報名學員數');
                }
                if (!Schema::hasColumn('courses', 'is_active')) {
                    $table->boolean('is_active')->default(true)->comment('是否啟用');
                }
            });
        }
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        // 不刪除表，以避免資料遺失
    }
};
