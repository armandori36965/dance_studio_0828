<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 執行遷移 - 為校區表新增類別欄位
     */
    public function up(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            // 新增校區類別欄位，預設為學校
            $table->enum('type', ['school', 'cram_school'])
                ->default('school')
                ->after('name')
                ->comment('校區類別：school=學校, cram_school=補習班');
        });
    }

    /**
     * 回滾遷移 - 移除校區類別欄位
     */
    public function down(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
