<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cln_x_visits', function (Blueprint $table) {
            $table->unsignedBigInteger('appointment_id')->nullable()->unique()->after('id');
            $table->unsignedBigInteger('doctor_id')->nullable()->index()->after('clinic');
            $table->timestamp('completed_at')->nullable()->after('status');
            $table->timestamp('updated_at')->nullable()->after('completed_at');
        });

        Schema::create('visit_vitals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('visit_id')->unique();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->unsignedSmallInteger('systolic_pressure')->nullable();
            $table->unsignedSmallInteger('diastolic_pressure')->nullable();
            $table->unsignedSmallInteger('pulse')->nullable();
            $table->unsignedSmallInteger('respiratory_rate')->nullable();
            $table->decimal('oxygen_saturation', 5, 2)->nullable();
            $table->decimal('weight', 6, 2)->nullable();
            $table->decimal('height', 6, 2)->nullable();
            $table->decimal('bmi', 5, 2)->nullable();
            $table->decimal('blood_sugar', 7, 2)->nullable();
            $table->unsignedBigInteger('recorded_by');
            $table->timestamp('measured_at')->nullable();
            $table->timestamps();
            $table->index('recorded_by');
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('visit_id')->unique();
            $table->unsignedInteger('patient_id')->index();
            $table->unsignedInteger('doctor_id')->index();
            $table->string('status', 20)->default('draft');
            $table->timestamp('issued_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->string('medication_name');
            $table->string('dosage', 120)->nullable();
            $table->string('frequency', 120)->nullable();
            $table->string('duration', 120)->nullable();
            $table->string('route', 120)->nullable();
            $table->text('instructions')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('visit_reopen_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('visit_id')->index();
            $table->unsignedBigInteger('reopened_by')->index();
            $table->text('reason');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_reopen_logs');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('visit_vitals');
        Schema::table('cln_x_visits', function (Blueprint $table) {
            $table->dropUnique(['appointment_id']);
            $table->dropIndex(['doctor_id']);
            $table->dropColumn(['appointment_id', 'doctor_id', 'completed_at', 'updated_at']);
        });
    }
};
