<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 為用戶表新增補習班ID欄位
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cram_school_id')
                ->nullable()
                ->after('campus_id')
                ->constrained('campuses')
                ->onDelete('set null')
                ->comment('補習班ID');
        });
    }

    /**
     * 回滾遷移 - 移除補習班ID欄位
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cram_school_id']);
            $table->dropColumn('cram_school_id');
        });
    }
};
