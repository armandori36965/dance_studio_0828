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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('課程名稱');
            $table->text('description')->nullable()->comment('課程描述');
            $table->decimal('price', 10, 2)->default(0)->comment('課程價格');
            $table->integer('duration')->comment('課程時長（分鐘）');
            $table->integer('max_students')->default(20)->comment('最大學生數');
            $table->foreignId('campus_id')->constrained()->onDelete('cascade')->comment('所屬校區');
            $table->string('level')->default('beginner')->comment('課程等級');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->timestamps();
        });
    }

    /**
     * 回滾遷移
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
