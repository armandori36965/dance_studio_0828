<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            // 添加效能索引
            $table->index('course_id', 'course_sessions_course_id_index'); // 關聯查詢索引
            $table->index('sort_order', 'course_sessions_sort_order_index'); // 排序索引
            $table->index('start_time', 'course_sessions_start_time_index'); // 時間排序索引
            $table->index('status', 'course_sessions_status_index'); // 狀態篩選索引

            // 複合索引 - 最常見的查詢模式：按課程ID查詢並按sort_order排序
            $table->index(['course_id', 'sort_order'], 'course_sessions_course_sort_index');

            // 複合索引 - 按課程ID和時間排序
            $table->index(['course_id', 'start_time'], 'course_sessions_course_time_index');

            // 複合索引 - 按課程ID和狀態篩選
            $table->index(['course_id', 'status'], 'course_sessions_course_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            // 移除索引（按相反順序）
            $table->dropIndex('course_sessions_course_status_index');
            $table->dropIndex('course_sessions_course_time_index');
            $table->dropIndex('course_sessions_course_sort_index');
            $table->dropIndex('course_sessions_status_index');
            $table->dropIndex('course_sessions_start_time_index');
            $table->dropIndex('course_sessions_sort_order_index');
            $table->dropIndex('course_sessions_course_id_index');
        });
    }
};
