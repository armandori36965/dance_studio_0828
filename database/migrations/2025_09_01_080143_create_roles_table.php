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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 角色名稱
            $table->text('permissions')->nullable(); // 權限列表（JSON格式）
            $table->text('description')->nullable(); // 角色描述
            $table->boolean('is_active')->default(true)->comment('是否啟用'); // 角色啟用狀態
            $table->boolean('requires_campus')->default(false)->comment('是否需要校區'); // 是否需要校區
            $table->timestamps();
        });
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
