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
        Schema::create('cln_x_visits_services', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->integer('visit_id')->nullable();
            $table->integer('clinic')->nullable();
            $table->integer('service')->nullable();
            $table->integer('status')->nullable()->default(0);
            $table->integer('patient');
            $table->integer('d_start');
            $table->integer('srv_type')->default(0);

            $table->index(['id', 'visit_id', 'patient', 'd_start'], 'cln_x_visit_srv');
            $table->index(['id', 'visit_id', 'patient'], 'cln_x_vix_srv');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cln_x_visits_services');
    }
};
