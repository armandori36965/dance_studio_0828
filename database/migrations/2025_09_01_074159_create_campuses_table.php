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
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('校區名稱');
            $table->string('address')->nullable()->comment('地址');
            $table->string('phone')->nullable()->comment('聯絡電話');
            $table->string('email')->nullable()->comment('聯絡信箱');
            $table->text('description')->nullable()->comment('校區描述');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->timestamps();
        });
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};
