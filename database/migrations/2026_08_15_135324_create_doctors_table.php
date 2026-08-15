<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->integer('act')->default(1);
            $table->tinyInteger('famous')->default(0)->index('famous');
            $table->string('name_ar');
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->integer('slot_time')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('photo')->nullable();
            $table->text('subgrp')->nullable();
            $table->integer('sex')->nullable()->default(1);
            $table->text('specialization_ar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctors');
    }
};
