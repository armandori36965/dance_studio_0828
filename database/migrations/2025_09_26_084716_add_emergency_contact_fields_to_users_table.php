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
        Schema::table('users', function (Blueprint $table) {
            $table->string('emergency_contact_name')->nullable()->comment('緊急聯絡人姓名')->after('parent_phone');
            $table->string('emergency_contact_phone')->nullable()->comment('緊急聯絡人電話')->after('emergency_contact_name');
            $table->unsignedBigInteger('cram_school_id')->nullable()->comment('補習班ID')->after('campus_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['emergency_contact_name', 'emergency_contact_phone', 'cram_school_id']);
        });
    }
};
