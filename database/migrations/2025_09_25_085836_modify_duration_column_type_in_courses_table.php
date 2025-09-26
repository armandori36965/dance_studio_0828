<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 修改 duration 欄位類型為 decimal
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // 將 duration 欄位從 int 改為 decimal(3,1)，支援一位小數
            $table->decimal('duration', 3, 1)->default(1.0)->change();
        });
    }

    /**
     * 回滾遷移 - 將 duration 欄位改回 int
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // 將 duration 欄位改回 int
            $table->integer('duration')->default(60)->change();
        });
    }
};
