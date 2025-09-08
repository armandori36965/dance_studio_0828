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
        Schema::create('school_events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // 事件標題
            $table->text('description')->nullable(); // 事件描述
            $table->datetime('start_date'); // 開始日期
            $table->datetime('end_date')->nullable(); // 結束日期
            $table->string('location')->nullable(); // 地點
            $table->string('status')->default('active'); // 狀態
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // 創建者
            $table->timestamps();
        });
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('school_events');
    }
};
