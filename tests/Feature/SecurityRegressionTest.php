<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function makeVehicle(User $user, array $overrides = []): Vehicle
    {
        return Vehicle::create(array_merge([
            'user_id' => $user->id,
            'type' => 'car',
            'nickname' => 'Carro de Teste',
            'brand' => 'Fiat',
            'model' => 'Uno',
            'year_manufacture' => 2020,
            'year_model' => 2021,
            'fuel_type' => 'flex',
            'current_mileage' => 10000,
            'average_monthly_mileage' => 1000,
            'usage_condition' => 'normal',
        ], $overrides));
    }

    private function makeSchedule(Vehicle $vehicle): VehicleMaintenanceSchedule
    {
        return VehicleMaintenanceSchedule::create([
            'vehicle_id' => $vehicle->id,
            'system_category' => 'engine_oil',
            'item_name' => 'Troca de Óleo',
            'interval_km' => 10000,
            'interval_months' => 12,
            'last_performed_mileage' => 0,
            'next_due_mileage' => 10000,
            'is_active' => true,
        ]);
    }

    public function test_user_cannot_register_maintenance_on_another_users_vehicle(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $victimVehicle = $this->makeVehicle($owner);

        $response = $this->actingAs($attacker)->post(route('maintenances.store'), [
            'vehicle_id' => $victimVehicle->id,
            'type' => 'preventive',
            'system_category' => 'engine_oil',
            'title' => 'Ataque',
            'performed_at' => now()->format('Y-m-d'),
            'mileage' => 999,
        ]);

        $response->assertSessionHasErrors('vehicle_id');
        $this->assertDatabaseCount('maintenance_records', 0);
    }

    public function test_user_cannot_tamper_with_another_users_schedule_via_schedule_id(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $victimSchedule = $this->makeSchedule($this->makeVehicle($owner));
        $attackerVehicle = $this->makeVehicle($attacker);

        // O atacante usa o PRÓPRIO veículo, mas aponta para o schedule da vítima (IDOR)
        $response = $this->actingAs($attacker)->post(route('maintenances.store'), [
            'vehicle_id' => $attackerVehicle->id,
            'schedule_id' => $victimSchedule->id,
            'type' => 'preventive',
            'system_category' => 'engine_oil',
            'title' => 'Tentativa de IDOR',
            'performed_at' => now()->format('Y-m-d'),
            'mileage' => 55555,
        ]);

        $response->assertSessionHasErrors('schedule_id');

        $victimSchedule->refresh();
        $this->assertEquals(0, $victimSchedule->last_performed_mileage);
        $this->assertEquals(10000, $victimSchedule->next_due_mileage);
    }

    public function test_user_cannot_access_or_modify_foreign_resources(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $vehicle = $this->makeVehicle($owner);

        $this->actingAs($attacker)->get(route('vehicles.show', $vehicle))->assertForbidden();
        $this->actingAs($attacker)->get(route('reports.export-csv', $vehicle))->assertForbidden();
        $this->actingAs($attacker)->get(route('reports.certificate', $vehicle))->assertForbidden();
        $this->actingAs($attacker)->post(route('vehicles.odometer.update', $vehicle), ['mileage' => 99999])->assertForbidden();
        $this->actingAs($attacker)->delete(route('vehicles.destroy', $vehicle))->assertForbidden();

        $this->assertEquals(10000, $vehicle->fresh()->current_mileage);
    }

    public function test_csv_export_neutralizes_formula_injection(): void
    {
        $user = User::factory()->create();
        $vehicle = $this->makeVehicle($user, ['nickname' => '=HYPERLINK("http://evil","x")']);

        $response = $this->actingAs($user)->get(route('reports.export-csv', $vehicle));
        $response->assertOk();

        $csv = $response->streamedContent();
        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringNotContainsString(',=HYPERLINK', $csv);
    }

    public function test_responses_include_security_headers(): void
    {
        $response = $this->get('/login');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_numeric_fields_reject_absurd_values(): void
    {
        $user = User::factory()->create();
        $vehicle = $this->makeVehicle($user);

        $this->actingAs($user)
            ->post(route('vehicles.odometer.update', $vehicle), ['mileage' => 99999999999])
            ->assertSessionHasErrors('mileage');

        $this->actingAs($user)
            ->post(route('fuels.store'), [
                'vehicle_id' => $vehicle->id,
                'date' => now()->format('Y-m-d'),
                'mileage' => 10100,
                'fuel_type' => 'gasoline',
                'liters' => 999999,
                'price_per_liter' => 5.9,
            ])
            ->assertSessionHasErrors('liters');
    }
}
