<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `prescription_items` MODIFY `dosage` VARCHAR(120) NULL');
        DB::statement('ALTER TABLE `prescription_items` MODIFY `frequency` VARCHAR(120) NULL');
        DB::statement('ALTER TABLE `prescription_items` MODIFY `duration` VARCHAR(120) NULL');
    }

    public function down(): void
    {
        DB::statement("UPDATE `prescription_items` SET `dosage` = '' WHERE `dosage` IS NULL");
        DB::statement("UPDATE `prescription_items` SET `frequency` = '' WHERE `frequency` IS NULL");
        DB::statement("UPDATE `prescription_items` SET `duration` = '' WHERE `duration` IS NULL");
        DB::statement('ALTER TABLE `prescription_items` MODIFY `dosage` VARCHAR(120) NOT NULL');
        DB::statement('ALTER TABLE `prescription_items` MODIFY `frequency` VARCHAR(120) NOT NULL');
        DB::statement('ALTER TABLE `prescription_items` MODIFY `duration` VARCHAR(120) NOT NULL');
    }
};
