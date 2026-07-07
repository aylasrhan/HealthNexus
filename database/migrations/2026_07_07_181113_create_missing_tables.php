<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. جدول المواعيد
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('appointment_for');
            $table->date('appointment_date');
            $table->time('time');
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });

        // 2. جدول المواعيد المتاحة للأطباء
        Schema::create('doctor_available_slots', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('doctor_id');
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        // 3. جدول المرضى (إذا كان النظام يحتاجه)
        if (!Schema::hasTable('gnr_m_patients')) {
            Schema::create('gnr_m_patients', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->bigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('doctor_available_slots');
        Schema::dropIfExists('gnr_m_patients');
    }
};