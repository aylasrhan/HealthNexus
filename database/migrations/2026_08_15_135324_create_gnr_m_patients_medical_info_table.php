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
        Schema::create('gnr_m_patients_medical_info', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->integer('id', true);
            $table->string('patient', 200)->unique('patient');
            $table->date('birth_date');
            $table->integer('sex')->nullable()->default(1);
            $table->integer('father_height')->nullable();
            $table->integer('mother_height')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gnr_m_patients_medical_info');
    }
};
