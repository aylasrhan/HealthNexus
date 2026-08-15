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
        Schema::create('gnr_m_patients', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->string('f_name', 200);
            $table->string('l_name', 200);
            $table->string('ft_name', 200);
            $table->string('mother_name');
            $table->string('plc_birth', 120);
            $table->string('no', 20);
            $table->string('mobile', 20);
            $table->date('birth_date');
            $table->integer('sex')->nullable()->default(1);
            $table->string('phone', 20)->nullable();
            $table->integer('date')->nullable();
            $table->string('blood', 20)->nullable();
            $table->integer('p_city')->default(0);
            $table->integer('p_area')->default(0);
            $table->string('profession', 200)->nullable();
            $table->integer('marital_status')->nullable();
            $table->integer('title')->nullable();
            $table->integer('nationality')->nullable();
            $table->integer('reach_reference')->nullable();
            $table->string('reach_reference_desc')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnr_m_patients');
    }
};
