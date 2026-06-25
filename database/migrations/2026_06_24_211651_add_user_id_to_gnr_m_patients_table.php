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
        Schema::table('gnr_m_patients', function (Blueprint $table) {
            // إضافة عمود user_id لربط المريض بحساب المستخدم
            $table->unsignedBigInteger('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gnr_m_patients', function (Blueprint $table) {
            // حذف العمود في حال التراجع
            $table->dropColumn('user_id');
        });
    }
};