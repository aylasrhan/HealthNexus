<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cln_m_services', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->default(0)->after('name_en');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number', 40)->unique();
            $table->unsignedInteger('visit_id')->unique();
            $table->unsignedInteger('patient_id')->index();
            $table->unsignedInteger('clinic_id')->nullable()->index();
            $table->unsignedInteger('doctor_id')->nullable()->index();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('status', 20)->default('unpaid')->index();
            $table->timestamp('issued_at')->nullable();
            $table->date('due_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('service_id')->nullable()->index();
            $table->string('description');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method', 30)->default('cash');
            $table->string('reference', 120)->nullable();
            $table->timestamp('paid_at');
            $table->unsignedBigInteger('received_by')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::table('cln_m_services', fn (Blueprint $table) => $table->dropColumn('price'));
    }
};
