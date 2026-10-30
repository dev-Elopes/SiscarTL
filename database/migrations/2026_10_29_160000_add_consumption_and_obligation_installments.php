<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Adicionar campos de consumo e tanque na tabela vehicles se não existirem
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'expected_km_per_liter')) {
                $table->decimal('expected_km_per_liter', 5, 2)->nullable()->after('average_monthly_mileage');
            }
            if (!Schema::hasColumn('vehicles', 'tank_capacity')) {
                $table->decimal('tank_capacity', 5, 2)->nullable()->after('expected_km_per_liter');
            }
        });

        // 2. Criar tabela associativa de parcelamento de obrigações/taxas
        if (!Schema::hasTable('vehicle_obligation_installments')) {
            Schema::create('vehicle_obligation_installments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vehicle_obligation_id')->constrained('vehicle_obligations')->cascadeOnDelete();
                $table->unsignedTinyInteger('installment_number');
                $table->unsignedTinyInteger('total_installments');
                $table->date('due_date');
                $table->decimal('amount', 10, 2);
                $table->string('status', 20)->default('pending'); // pending, paid, overdue
                $table->date('paid_at')->nullable();
                $table->decimal('amount_paid', 10, 2)->nullable();
                $table->string('payment_method', 50)->nullable();
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_obligation_installments');

        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'tank_capacity')) {
                $table->dropColumn('tank_capacity');
            }
            if (Schema::hasColumn('vehicles', 'expected_km_per_liter')) {
                $table->dropColumn('expected_km_per_liter');
            }
        });
    }
};
