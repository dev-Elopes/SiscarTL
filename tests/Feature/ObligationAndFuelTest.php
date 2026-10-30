<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleObligation;
use App\Models\VehicleObligationInstallment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObligationAndFuelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    
    public function test_can_create_installment_obligation_and_pay_each_installment(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'type' => 'car',
            'nickname' => 'Gol 1.6',
            'brand' => 'Volkswagen',
            'model' => 'Gol',
            'year_manufacture' => 2020,
            'year_model' => 2021,
            'fuel_type' => 'flex',
            'current_mileage' => 50000,
            'average_monthly_mileage' => 1200,
            'expected_km_per_liter' => 11.5,
            'tank_capacity' => 50,
            'usage_condition' => 'normal',
        ]);

        $this->actingAs($user);

        // 1. Cadastrar IPVA com 3 parcelas de R$ 900 total (3x R$ 300)
        $response = $this->post(route('obligations.store'), [
            'vehicle_id' => $vehicle->id,
            'type' => 'ipva',
            'title' => 'IPVA 2026 Parcelado',
            'reference_year' => 2026,
            'due_date' => '2026-03-10',
            'amount' => 900.00,
            'status' => 'pending',
            'payment_mode' => 'installments',
            'installments_count' => 3,
        ]);

        $response->assertRedirect();

        $obligation = VehicleObligation::where('vehicle_id', $vehicle->id)->first();
        $this->assertNotNull($obligation);
        $this->assertEquals(900.00, (float) $obligation->amount);
        $this->assertEquals(3, $obligation->total_installments);

        // Verificar se foram geradas exatamente 3 parcelas na tabela associativa
        $installments = $obligation->installments;
        $this->assertCount(3, $installments);
        $this->assertEquals(300.00, (float) $installments[0]->amount);
        $this->assertEquals(1, $installments[0]->installment_number);
        $this->assertEquals('pending', $installments[0]->status);

        // 2. Quitar a 1ª parcela
        $payResp = $this->patch(route('obligations.installments.pay', $installments[0]));
        $payResp->assertRedirect();

        $installments[0]->refresh();
        $obligation->refresh();

        $this->assertEquals('paid', $installments[0]->status);
        $this->assertEquals(300.00, (float) $obligation->amount_paid);
        $this->assertEquals('pending', $obligation->status); // Ainda tem 2 pendentes

        // 3. Quitar as outras 2 parcelas
        $this->patch(route('obligations.installments.pay', $installments[1]));
        $this->patch(route('obligations.installments.pay', $installments[2]));

        $obligation->refresh();
        $this->assertEquals('paid', $obligation->status);
        $this->assertEquals(900.00, (float) $obligation->amount_paid);
        $this->assertNotNull($obligation->paid_at);

        // 4. Reabrir a 2ª parcela (desfazer)
        $this->patch(route('obligations.installments.reopen', $installments[1]));
        $obligation->refresh();
        $this->assertEquals('pending', $obligation->status);
        $this->assertEquals(600.00, (float) $obligation->amount_paid);
    }

    public function test_fuel_telemetry_page_loads_with_bi_metrics()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'type' => 'car',
            'nickname' => 'Onix Turbo',
            'brand' => 'Chevrolet',
            'model' => 'Onix',
            'year_manufacture' => 2022,
            'year_model' => 2023,
            'fuel_type' => 'flex',
            'current_mileage' => 30000,
            'average_monthly_mileage' => 1000,
            'expected_km_per_liter' => 13.5,
            'tank_capacity' => 44,
            'usage_condition' => 'severe',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('fuels.index'));
        $response->assertOk();
        $response->assertSee('Telemetria de Abastecimento');
        $response->assertSee('Autonomia Estimada');
        $response->assertSee('Evolução Mensal de Abastecimentos');
    }
}
