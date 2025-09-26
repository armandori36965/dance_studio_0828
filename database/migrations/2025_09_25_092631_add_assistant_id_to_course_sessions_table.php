<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 為 course_sessions 表添加 assistant_id 欄位
     */
    public function up(): void
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            // 添加助教欄位
            $table->foreignId('assistant_id')->nullable()->constrained('users')->onDelete('set null')->comment('助教')->after('teacher_id');
        });
    }

    /**
     * 回滾遷移 - 移除 assistant_id 欄位
     */
    public function down(): void
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            $table->dropForeign(['assistant_id']);
            $table->dropColumn('assistant_id');
        });
    }
};
