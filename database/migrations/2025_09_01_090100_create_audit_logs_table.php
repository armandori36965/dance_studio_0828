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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // 用戶ID
            $table->string('action'); // 操作類型
            $table->string('model_type'); // 模型類型
            $table->unsignedBigInteger('model_id'); // 模型ID
            $table->json('old_values')->nullable(); // 舊值
            $table->json('new_values')->nullable(); // 新值
            $table->string('ip_address', 45)->nullable(); // IP地址
            $table->text('user_agent')->nullable(); // 用戶代理
            $table->timestamps();

            // 索引
            $table->index(['model_type', 'model_id']);
            $table->index('user_id');
            $table->index('action');
        });
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
