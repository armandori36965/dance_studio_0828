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
            // 添加授課老師欄位
            $table->unsignedBigInteger('teacher_id')->nullable()->after('course_id');

            // 添加外鍵約束
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');

            // 添加索引提升查詢效能
            $table->index('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            // 移除外鍵和索引
            $table->dropForeign(['teacher_id']);
            $table->dropIndex(['teacher_id']);

            // 移除欄位
            $table->dropColumn('teacher_id');
        });
    }
};
