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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id'); // معرف الطبيب (وهو مستخدم في جدول users)
            $table->unsignedBigInteger('user_id');   // معرف المريض الذي قام بالتقييم
            $table->integer('rating');               // قيمة التقييم (من 1 إلى 5)
            $table->text('comment')->nullable();     // تعليق اختياري
            $table->timestamps();

            // الربط الصحيح مع جدول users للطرفين (الطبيب والمريض)
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};