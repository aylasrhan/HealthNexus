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
        Schema::create('cln_m_medical_his', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->integer('cat')->nullable();
            $table->string('name_ar');
            $table->string('name_en');
            $table->integer('s_date')->nullable()->default(0);
            $table->integer('e_date')->nullable()->default(0);
            $table->integer('num')->nullable()->default(0);
            $table->integer('note')->nullable()->default(0);
            $table->integer('active')->nullable();
            $table->integer('act')->nullable()->default(1);
            $table->integer('alert')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cln_m_medical_his');
    }
};
