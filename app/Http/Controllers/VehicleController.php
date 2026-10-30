<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceCatalog;
use App\Models\OdometerLog;
use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleMaintenanceSchedule;
use App\Models\VehicleModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = $request->user()->vehicles()
            ->with(['schedules', 'odometerLogs'])
            ->latest()
            ->get();

        return view('veiculos.index', compact('vehicles'));
    }

    public function create()
    {
        $brands = VehicleBrand::with('models')->orderBy('name')->get();
        return view('veiculos.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:car,motorcycle',
            'nickname' => 'required|string|max:100',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year_manufacture' => 'required|integer|min:1950|max:'.(date('Y') + 1),
            'year_model' => 'required|integer|min:1950|max:'.(date('Y') + 2),
            'fuel_type' => 'required|in:gasoline,ethanol,flex,diesel,cng,electric,hybrid',
            'current_mileage' => 'required|integer|min:0|max:9999999',
            'average_monthly_mileage' => 'nullable|integer|min:100|max:20000',
            'expected_km_per_liter' => 'nullable|numeric|min:1|max:99',
            'tank_capacity' => 'nullable|numeric|min:1|max:500',
            'license_plate' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
            'chassis' => 'nullable|string|max:30',
            'renavam' => 'nullable|string|max:20',
            'transmission_type' => 'nullable|string|max:30',
            'secondary_drive_type' => 'nullable|string|max:30',
            'usage_condition' => 'required|in:normal,severe',
            'photo' => 'nullable|image|max:3072',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('vehicles', 'public');
        }

        $validated['user_id'] = $request->user()->id;
        $validated['average_monthly_mileage'] = $validated['average_monthly_mileage'] ?? 1000;

        $vehicle = Vehicle::create($validated);

        // Registrar primeiro log de odômetro
        OdometerLog::create([
            'vehicle_id' => $vehicle->id,
            'mileage' => $vehicle->current_mileage,
            'recorded_at' => now(),
            'source' => 'manual',
            'notes' => 'Quilometragem inicial de cadastro',
        ]);

        // Gerar automaticamente o Plano de Manutenção Preventiva em Camadas (RF05 e RF06)
        $catalogs = MaintenanceCatalog::whereIn('vehicle_type', [$vehicle->type, 'all'])->get();
        $isSevere = $vehicle->usage_condition === 'severe';

        foreach ($catalogs as $cat) {
            $intervalKm = $isSevere ? ($cat->severe_interval_km ?? $cat->default_interval_km) : $cat->default_interval_km;
            $intervalMonths = $isSevere ? ($cat->severe_interval_months ?? $cat->default_interval_months) : $cat->default_interval_months;

            VehicleMaintenanceSchedule::create([
                'vehicle_id' => $vehicle->id,
                'catalog_id' => $cat->id,
                'system_category' => $cat->system_category,
                'item_name' => $cat->item_name,
                'interval_km' => $intervalKm,
                'interval_months' => $intervalMonths,
                'last_performed_at' => now(),
                'last_performed_mileage' => $vehicle->current_mileage,
                'next_due_mileage' => $intervalKm ? ($vehicle->current_mileage + $intervalKm) : null,
                'next_due_date' => $intervalMonths ? now()->addMonths($intervalMonths) : null,
                'is_active' => true,
                'notes' => $cat->description,
            ]);
        }

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Veículo cadastrado e plano de manutenção preventiva gerado com sucesso!');
    }

    public function show(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        $vehicle->load([
            'schedules.catalog',
            'maintenanceRecords.items',
            'fuelRecords',
            'obligations',
            'odometerLogs',
        ]);

        // Estatísticas financeiras do veículo
        $totalMaintenance = $vehicle->maintenanceRecords->sum('total_cost');
        $totalFuel = $vehicle->fuelRecords->sum('total_cost');
        $totalObligations = $vehicle->obligations->where('status', 'paid')->sum('amount_paid');
        $grandTotal = $totalMaintenance + $totalFuel + $totalObligations;

        // Consumo médio
        $fuelEntriesWithLiters = $vehicle->fuelRecords->where('liters', '>', 0);
        $totalLiters = $fuelEntriesWithLiters->sum('liters');
        $avgKmLiter = $vehicle->fuelRecords->whereNotNull('km_per_liter')->avg('km_per_liter');

        return view('veiculos.show', compact(
            'vehicle',
            'totalMaintenance',
            'totalFuel',
            'totalObligations',
            'grandTotal',
            'totalLiters',
            'avgKmLiter'
        ));
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);
        $brands = VehicleBrand::with('models')->orderBy('name')->get();

        return view('veiculos.edit', compact('vehicle', 'brands'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        $validated = $request->validate([
            'nickname' => 'required|string|max:100',
            'average_monthly_mileage' => 'nullable|integer|min:100|max:20000',
            'expected_km_per_liter' => 'nullable|numeric|min:1|max:99',
            'tank_capacity' => 'nullable|numeric|min:1|max:500',
            'license_plate' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
            'chassis' => 'nullable|string|max:30',
            'renavam' => 'nullable|string|max:20',
            'transmission_type' => 'nullable|string|max:30',
            'secondary_drive_type' => 'nullable|string|max:30',
            'usage_condition' => 'required|in:normal,severe',
            'status' => 'required|in:active,sold,inactive',
            'photo' => 'nullable|image|max:3072',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            if ($vehicle->photo_path) {
                Storage::disk('public')->delete($vehicle->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('vehicles', 'public');
        }

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Dados do veículo atualizados com sucesso!');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Veículo removido da garagem.');
    }

    public function updateMileage(Request $request, Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        // Aceita tanto 'mileage' quanto 'current_mileage' do formulário
        $mileage = $request->input('mileage') ?? $request->input('current_mileage');
        $request->merge([
            'mileage' => $mileage,
            'recorded_at' => $request->input('recorded_at') ?? now()->format('Y-m-d'),
        ]);

        $validated = $request->validate([
            'mileage' => 'required|integer|max:9999999|gte:'.$vehicle->current_mileage,
            'recorded_at' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ], [
            'mileage.required' => 'Informe a nova quilometragem do veículo.',
            'mileage.integer' => 'A quilometragem deve ser um número inteiro.',
            'mileage.gte' => 'O novo odômetro não pode ser menor que o atual ('.number_format($vehicle->current_mileage, 0, ',', '.').' km).',
        ]);

        $vehicle->update(['current_mileage' => $validated['mileage']]);

        OdometerLog::create([
            'vehicle_id' => $vehicle->id,
            'mileage' => $validated['mileage'],
            'recorded_at' => $validated['recorded_at'],
            'source' => 'manual',
            'notes' => $validated['notes'] ?? 'Atualização rápida pelo painel',
        ]);

        return back()->with('success', 'Odômetro atualizado com sucesso para ' . number_format($validated['mileage'], 0, ',', '.') . ' km!');
    }

    public function simulateTestData(Request $request, Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        // Endpoint de demonstração: nunca deve existir em produção (polui dados reais)
        abort_unless(app()->environment(['local', 'testing']), 404);

        $userId = $request->user()->id;

        // 1. Criar Revisão Passada de Óleo e Filtros
        $lastMileage = max(0, $vehicle->current_mileage - 4500);
        $maint1 = \App\Models\MaintenanceRecord::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $userId,
            'type' => 'preventive',
            'system_category' => 'engine_oil',
            'title' => 'Troca de Óleo 5W30 Sintético + Filtros',
            'description' => 'Substituição preventiva de óleo lubrificante, filtro de óleo e filtro de ar.',
            'performed_at' => now()->subMonths(3),
            'mileage' => $lastMileage,
            'workshop_name' => 'Auto Mecânica Especializada',
            'labor_cost' => 120.00,
            'parts_cost' => 260.00,
            'total_cost' => 380.00,
            'warranty_days' => 90,
            'oil_brand' => 'Mobil Super',
            'oil_viscosity' => '5W-30',
        ]);

        \App\Models\MaintenanceRecordItem::create([
            'maintenance_record_id' => $maint1->id,
            'item_name' => 'Óleo Sintético 5W-30 (4 Litros)',
            'quantity' => 1,
            'unit_price' => 190.00,
            'total_price' => 190.00,
        ]);
        \App\Models\MaintenanceRecordItem::create([
            'maintenance_record_id' => $maint1->id,
            'item_name' => 'Filtro de Óleo',
            'quantity' => 1,
            'unit_price' => 40.00,
            'total_price' => 40.00,
        ]);
        \App\Models\MaintenanceRecordItem::create([
            'maintenance_record_id' => $maint1->id,
            'item_name' => 'Filtro de Ar do Motor',
            'quantity' => 1,
            'unit_price' => 30.00,
            'total_price' => 30.00,
        ]);

        // Atualizar schedule de óleo
        $oilSched = $vehicle->schedules()->where('system_category', 'engine_oil')->first();
        if ($oilSched) {
            $oilSched->update([
                'last_performed_at' => now()->subMonths(3),
                'last_performed_mileage' => $lastMileage,
                'next_due_mileage' => $lastMileage + ($oilSched->interval_km ?: 10000),
                'next_due_date' => now()->subMonths(3)->addMonths($oilSched->interval_months ?: 12),
            ]);
        }

        // 2. Criar 2 registros de Abastecimento com cálculo de consumo
        \App\Models\FuelRecord::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $userId,
            'date' => now()->subDays(15),
            'mileage' => max(0, $vehicle->current_mileage - 550),
            'fuel_type' => 'gasoline',
            'liters' => 45.0,
            'price_per_liter' => 5.89,
            'total_cost' => 265.05,
            'is_full_tank' => true,
            'gas_station' => 'Posto Shell Select',
        ]);

        \App\Models\FuelRecord::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $userId,
            'date' => now()->subDays(2),
            'mileage' => $vehicle->current_mileage,
            'fuel_type' => 'gasoline',
            'liters' => 40.5,
            'price_per_liter' => 5.92,
            'total_cost' => 239.76,
            'is_full_tank' => true,
            'km_per_liter' => 13.6,
            'gas_station' => 'Posto Ipiranga Rodo',
        ]);

        // 3. Criar Obrigação Fiscal (IPVA Quitado)
        \App\Models\VehicleObligation::firstOrCreate([
            'vehicle_id' => $vehicle->id,
            'type' => 'ipva',
            'reference_year' => date('Y'),
        ], [
            'title' => 'IPVA Cota Única (com 3% desconto)',
            'amount' => 1845.50,
            'due_date' => now()->subMonths(2),
            'status' => 'paid',
            'notes' => 'Pago via Pix com autenticação bancária',
        ]);

        return back()->with('success', 'Histórico de demonstração gerado com sucesso! Foram preenchidas manutenções, telemetria de 13.6 km/l e IPVA quitado.');
    }

    private function authorizeVehicle(Vehicle $vehicle): void
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado a este veículo.');
        }
    }
}
