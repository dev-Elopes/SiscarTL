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
        // 1. Catálogo de Marcas
        Schema::create('vehicle_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('car'); // car, motorcycle, both
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });

        // 2. Catálogo de Modelos
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('vehicle_brands')->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('car'); // car, motorcycle
            $table->string('category')->nullable(); // Hatch, Sedan, SUV, etc.
            $table->timestamps();
        });

        // 3. Veículos do Usuário
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default('car'); // car, motorcycle
            $table->string('nickname');
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year_manufacture');
            $table->unsignedSmallInteger('year_model');
            $table->string('fuel_type')->default('flex'); // gasoline, ethanol, flex, diesel, cng, electric, hybrid
            $table->unsignedInteger('current_mileage');
            $table->unsignedInteger('average_monthly_mileage')->default(1000);
            $table->string('license_plate', 10)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('chassis', 30)->nullable();
            $table->string('renavam', 20)->nullable();
            $table->string('transmission_type', 30)->nullable(); // manual, automatic, cvt, automated
            $table->string('secondary_drive_type', 30)->nullable(); // chain, belt, shaft (motos)
            $table->string('usage_condition', 20)->default('normal'); // normal, severe
            $table->string('status', 20)->default('active'); // active, sold, inactive
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Histórico de Odômetro
        Schema::create('odometer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->unsignedInteger('mileage');
            $table->date('recorded_at');
            $table->string('source', 30)->default('manual'); // manual, maintenance, refuel
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 5. Catálogo Base de Planos de Manutenção Preventiva
        Schema::create('maintenance_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type')->default('all'); // car, motorcycle, all
            $table->string('system_category'); // engine_oil, transmission, brakes, suspension_steering, cooling_climate, electrical, tires_wheels, motorcycle_drive
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->unsignedInteger('default_interval_km')->nullable();
            $table->unsignedInteger('default_interval_months')->nullable();
            $table->unsignedInteger('severe_interval_km')->nullable();
            $table->unsignedInteger('severe_interval_months')->nullable();
            $table->boolean('is_critical')->default(false);
            $table->timestamps();
        });

        // 6. Cronograma / Itens Preventivos do Veículo
        Schema::create('vehicle_maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('catalog_id')->nullable()->constrained('maintenance_catalogs')->nullOnDelete();
            $table->string('system_category');
            $table->string('item_name');
            $table->unsignedInteger('interval_km')->nullable();
            $table->unsignedInteger('interval_months')->nullable();
            $table->date('last_performed_at')->nullable();
            $table->unsignedInteger('last_performed_mileage')->nullable();
            $table->unsignedInteger('next_due_mileage')->nullable();
            $table->date('next_due_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Registros de Manutenções Realizadas (Prontuário)
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('vehicle_maintenance_schedules')->nullOnDelete();
            $table->string('type')->default('preventive'); // preventive, corrective, oil_change
            $table->string('system_category')->default('engine_oil');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('performed_at');
            $table->unsignedInteger('mileage');
            $table->decimal('parts_cost', 10, 2)->default(0);
            $table->decimal('labor_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->string('workshop_name')->nullable();
            $table->string('workshop_phone')->nullable();
            $table->string('technician_name')->nullable();
            $table->string('oil_viscosity')->nullable(); // ex: 5W30 Sintético
            $table->string('oil_brand')->nullable();
            $table->decimal('oil_liters', 4, 2)->nullable();
            $table->json('filters_replaced')->nullable(); // ['oil', 'air', 'fuel', 'cabin']
            $table->unsignedSmallInteger('warranty_days')->nullable();
            $table->unsignedInteger('warranty_km')->nullable();
            $table->string('receipt_photo_path')->nullable();
            $table->string('invoice_url')->nullable();
            $table->timestamps();
        });

        // 8. Peças/Itens Individuais da Manutenção
        Schema::create('maintenance_record_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_record_id')->constrained('maintenance_records')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('brand')->nullable();
            $table->string('part_number')->nullable();
            $table->decimal('quantity', 6, 2)->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });

        // 9. Registros de Abastecimento
        Schema::create('fuel_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('mileage');
            $table->string('fuel_type')->default('gasoline'); // gasoline, ethanol, diesel, cng
            $table->decimal('liters', 6, 2);
            $table->decimal('price_per_liter', 6, 2);
            $table->decimal('total_cost', 10, 2);
            $table->boolean('is_full_tank')->default(true);
            $table->decimal('km_per_liter', 6, 2)->nullable();
            $table->string('gas_station')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 10. Obrigações, Taxas e Documentação
        Schema::create('vehicle_obligations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('type')->default('ipva'); // ipva, licensing, insurance, dpvat, fine, inspection
            $table->string('title');
            $table->unsignedSmallInteger('reference_year');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->string('status', 20)->default('pending'); // pending, paid, overdue
            $table->date('paid_at')->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->unsignedTinyInteger('installment_number')->nullable();
            $table->unsignedTinyInteger('total_installments')->nullable();
            $table->string('receipt_image_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 11. Preferências de Notificação do Usuário
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('email_alerts_enabled')->default(true);
            $table->unsignedInteger('alert_km_before')->default(2000);
            $table->json('alert_days_before')->nullable(); // [40, 20, 5]
            $table->boolean('notify_overdue')->default(true);
            $table->boolean('notify_monthly_odometer_reminder')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('vehicle_obligations');
        Schema::dropIfExists('fuel_records');
        Schema::dropIfExists('maintenance_record_items');
        Schema::dropIfExists('maintenance_records');
        Schema::dropIfExists('vehicle_maintenance_schedules');
        Schema::dropIfExists('maintenance_catalogs');
        Schema::dropIfExists('odometer_logs');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_models');
        Schema::dropIfExists('vehicle_brands');
    }
};
