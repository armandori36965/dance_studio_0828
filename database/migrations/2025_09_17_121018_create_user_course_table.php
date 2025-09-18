<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 建立用戶課程關聯表
     */
    public function up(): void
    {
        Schema::create('user_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('用戶ID');
            $table->foreignId('course_id')->constrained()->onDelete('cascade')->comment('課程ID');
            $table->timestamps();

            // 確保同一用戶不能重複報名同一課程
            $table->unique(['user_id', 'course_id']);
        });
    }

    /**
     * 回滾遷移 - 刪除用戶課程關聯表
     */
    public function down(): void
    {
        Schema::dropIfExists('user_course');
    }
};
