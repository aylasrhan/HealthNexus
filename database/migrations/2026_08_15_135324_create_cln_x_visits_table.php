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
        Schema::create('cln_x_visits', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->integer('patient')->nullable();
            $table->integer('clinic')->nullable();
            $table->integer('type')->nullable()->default(1);
            $table->integer('d_start')->nullable();
            $table->integer('status')->nullable()->default(0);
            $table->text('note')->nullable();
            $table->integer('sub_status')->nullable()->default(0);
            $table->integer('new_pat')->default(0);

            $table->index(['id', 'patient', 'd_start'], 'cln_x_vis');
            $table->index(['id', 'd_start'], 'cln_x_visit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cln_x_visits');
    }
};
