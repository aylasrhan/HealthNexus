<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('cln_x_medical_his', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->integer('cat')->nullable();
            $table->integer('med_id')->nullable();
            $table->integer('s_date')->nullable();
            $table->integer('e_date')->nullable();
            $table->float('num')->default(0);
            $table->integer('active')->nullable();
            $table->text('note')->nullable();
            $table->integer('date')->nullable();
            $table->integer('patient')->nullable();
            $table->integer('doc')->nullable();
            $table->integer('alert')->default(0);
            $table->char('year', 4)->default('0');
        });

        DB::statement('ALTER TABLE `cln_x_medical_his` MODIFY `num` FLOAT NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cln_x_medical_his');
    }
};
