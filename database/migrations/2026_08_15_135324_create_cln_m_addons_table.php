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
        Schema::create('cln_m_addons', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->char('code', 10)->charset('latin1')->collation('latin1_swedish_ci')->nullable();
            $table->string('icon', 80)->nullable();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('addon', 40)->nullable();
            $table->integer('clinic')->nullable();
            $table->integer('service')->nullable();
            $table->integer('req_load')->nullable();
            $table->integer('req')->nullable();
            $table->integer('ord')->nullable();
            $table->char('short_code', 3)->charset('latin1')->collation('latin1_swedish_ci')->nullable();
            $table->integer('act')->nullable()->default(1);
            $table->string('color', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cln_m_addons');
    }
};
