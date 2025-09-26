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
        Schema::table('user_course', function (Blueprint $table) {
            // 修改 enrolled_at 欄位，允許為空並設定預設值
            $table->timestamp('enrolled_at')->nullable()->default(now())->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_course', function (Blueprint $table) {
            // 恢復原來的設定
            $table->timestamp('enrolled_at')->nullable(false)->change();
        });
    }
};
