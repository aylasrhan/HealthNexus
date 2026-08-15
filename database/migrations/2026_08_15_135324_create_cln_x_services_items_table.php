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
        Schema::create('cln_x_services_items', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->integer('visit')->nullable();
            $table->integer('srv')->nullable();
            $table->integer('iteme')->nullable();
            $table->integer('qunt')->nullable()->default(1);
            $table->integer('r_qunt')->nullable();
            $table->float('t_price')->nullable();
            $table->integer('status')->nullable();
            $table->integer('date')->nullable();
            $table->integer('doc')->nullable();
            $table->integer('clinic')->nullable();
        });

        DB::statement('ALTER TABLE `cln_x_services_items` MODIFY `t_price` FLOAT NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cln_x_services_items');
    }
};
