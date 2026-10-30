<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('email', 'demo@veiculos.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Appointments');
        $response->assertSee('Onix Plus Turbo');
    }

    public function test_user_can_view_vehicle_prontuario(): void
    {
        $user = User::where('email', 'demo@veiculos.com')->first();
        $vehicle = Vehicle::where('user_id', $user->id)->first();
        $this->assertNotNull($vehicle);

        $response = $this->actingAs($user)->get('/vehicles/' . $vehicle->id);
        $response->assertStatus(200);
        $response->assertSee($vehicle->nickname);
        $response->assertSee('Plano Preventivo');
    }

    public function test_user_can_access_maintenances_and_fuels(): void
    {
        $user = User::where('email', 'demo@veiculos.com')->first();

        $responseMaint = $this->actingAs($user)->get('/maintenances');
        $responseMaint->assertStatus(200);

        $responseFuels = $this->actingAs($user)->get('/fuels');
        $responseFuels->assertStatus(200);

        $responseObligations = $this->actingAs($user)->get('/obligations');
        $responseObligations->assertStatus(200);

        $responseReports = $this->actingAs($user)->get('/reports');
        $responseReports->assertStatus(200);
    }

    public function test_user_can_view_printable_certificate(): void
    {
        $user = User::where('email', 'demo@veiculos.com')->first();
        $vehicle = Vehicle::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)->get('/reports/certificate/' . $vehicle->id);
        $response->assertStatus(200);
        $response->assertSee('PRONTUÁRIO TÉCNICO DE REVISÕES');
    }
}
