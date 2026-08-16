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
        Schema::create('cln_m_services', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->string('code', 80);
            $table->integer('clinic')->nullable();
            $table->string('name_ar');
            $table->string('name_en');
            $table->integer('ord')->nullable()->default(1);
            $table->integer('act')->nullable()->default(1);
            $table->integer('rev')->default(0);
            $table->integer('ser_time')->nullable()->default(0);
            $table->integer('rev_time')->nullable()->default(0);
            $table->integer('multi')->default(0);
            $table->integer('def')->default(0);
            $table->integer('dis')->default(0);
            $table->integer('opr_type')->default(0);
            $table->integer('bty')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cln_m_services');
    }
};
