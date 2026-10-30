<?php

namespace App\Http\Controllers;

use App\Models\FuelRecord;
use App\Models\OdometerLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FuelController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles()->get();

        $selectedVehicleId = $request->input('vehicle_id');
        $selectedVehicle = $selectedVehicleId ? $vehicles->firstWhere('id', $selectedVehicleId) : $vehicles->first();

        $query = FuelRecord::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->with('vehicle')
            ->orderByDesc('date')
            ->orderByDesc('mileage');

        if ($selectedVehicleId) {
            $query->where('vehicle_id', $selectedVehicleId);
        }

        $records = $query->paginate(15)->withQueryString();

        $allQuery = FuelRecord::whereIn('vehicle_id', $vehicles->pluck('id'));
        if ($selectedVehicleId) {
            $allQuery->where('vehicle_id', $selectedVehicleId);
        }

        $totalSpent = (clone $allQuery)->sum('total_cost');
        $totalLiters = (clone $allQuery)->sum('liters');
        $avgKmLiter = (clone $allQuery)->whereNotNull('km_per_liter')->avg('km_per_liter');

        // BI Analytics: Agrupamento dos últimos 6 meses
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $months->put($key, [
                'label' => ucfirst($date->locale('pt_BR')->translatedFormat('M/y')),
                'liters' => 0,
                'cost' => 0,
            ]);
        }

        $monthlyData = (clone $allQuery)
            ->where('date', '>=', now()->subMonths(6)->startOfMonth())
            ->get();

        foreach ($monthlyData as $row) {
            $key = \Carbon\Carbon::parse($row->date)->format('Y-m');
            if ($months->has($key)) {
                $item = $months->get($key);
                $item['liters'] += (float) $row->liters;
                $item['cost'] += (float) $row->total_cost;
                $months->put($key, $item);
            }
        }

        $chartLabels = $months->pluck('label')->values()->toArray();
        $chartLiters = $months->pluck('liters')->map(fn($v) => round($v, 1))->values()->toArray();
        $chartCosts = $months->pluck('cost')->map(fn($v) => round($v, 2))->values()->toArray();

        // Dados de Histórico Recente para Linha de Eficiência
        $recentFuels = (clone $allQuery)
            ->orderBy('date')
            ->orderBy('mileage')
            ->take(12)
            ->get();

        $efficiencyLabels = $recentFuels->map(fn($f) => \Carbon\Carbon::parse($f->date)->format('d/m'))->values()->toArray();
        $efficiencyKmPerLiter = $recentFuels->map(fn($f) => $f->km_per_liter ? (float) $f->km_per_liter : null)->values()->toArray();
        $efficiencyPrice = $recentFuels->map(fn($f) => (float) $f->price_per_liter)->values()->toArray();

        // Estimativas de Consumo e Custo por KM
        $expectedConsumption = (float) ($selectedVehicle?->expected_km_per_liter ?: ($avgKmLiter ?: 12.0));
        $avgPricePerLiter = $totalLiters > 0 ? ($totalSpent / $totalLiters) : 5.89;
        $costPerKm = $expectedConsumption > 0 ? round($avgPricePerLiter / $expectedConsumption, 2) : 0.45;
        $monthlyKm = (int) ($selectedVehicle?->average_monthly_mileage ?: 1000);
        $estimatedMonthlyExpense = round(($monthlyKm / ($expectedConsumption ?: 12)) * $avgPricePerLiter, 2);

        return view('abastecimentos.index', compact(
            'records',
            'vehicles',
            'selectedVehicle',
            'selectedVehicleId',
            'totalSpent',
            'totalLiters',
            'avgKmLiter',
            'chartLabels',
            'chartLiters',
            'chartCosts',
            'efficiencyLabels',
            'efficiencyKmPerLiter',
            'efficiencyPrice',
            'expectedConsumption',
            'avgPricePerLiter',
            'costPerKm',
            'estimatedMonthlyExpense'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', Rule::exists('vehicles', 'id')->where('user_id', $request->user()->id)->whereNull('deleted_at')],
            'date' => 'required|date|before_or_equal:today',
            'mileage' => 'required|integer|min:0|max:9999999',
            'fuel_type' => 'required|in:gasoline,ethanol,diesel,cng',
            'liters' => 'required|numeric|min:0.5|max:9999',
            'price_per_liter' => 'required|numeric|min:0.1|max:99',
            'is_full_tank' => 'nullable|boolean',
            'gas_station' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        if ($vehicle->user_id !== $request->user()->id) {
            abort(403);
        }

        $liters = (float) $validated['liters'];
        $price = (float) $validated['price_per_liter'];
        $totalCost = round($liters * $price, 2);
        $isFullTank = $request->boolean('is_full_tank', true);

        // Calcular consumo (km/l) se for tanque cheio e houver abastecimento anterior tanque cheio
        $kmPerLiter = null;
        if ($isFullTank) {
            $previousFullFuel = FuelRecord::where('vehicle_id', $vehicle->id)
                ->where('is_full_tank', true)
                ->where('mileage', '<', $validated['mileage'])
                ->orderByDesc('mileage')
                ->first();

            if ($previousFullFuel) {
                $kmDiff = $validated['mileage'] - $previousFullFuel->mileage;
                if ($kmDiff > 0 && $liters > 0) {
                    $kmPerLiter = round($kmDiff / $liters, 2);
                }
            }
        }

        FuelRecord::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $request->user()->id,
            'date' => $validated['date'],
            'mileage' => $validated['mileage'],
            'fuel_type' => $validated['fuel_type'],
            'liters' => $liters,
            'price_per_liter' => $price,
            'total_cost' => $totalCost,
            'is_full_tank' => $isFullTank,
            'km_per_liter' => $kmPerLiter,
            'gas_station' => $validated['gas_station'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Atualizar odômetro do veículo se for mais recente
        if ($validated['mileage'] > $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $validated['mileage']]);
            OdometerLog::create([
                'vehicle_id' => $vehicle->id,
                'mileage' => $validated['mileage'],
                'recorded_at' => $validated['date'],
                'source' => 'refuel',
                'notes' => 'Abastecimento em '.($validated['gas_station'] ?? 'Posto'),
            ]);
        }

        return back()->with('success', 'Abastecimento registrado com sucesso!'.($kmPerLiter ? " Consumo calculado: {$kmPerLiter} km/l." : ''));
    }

    public function destroy(FuelRecord $fuel)
    {
        if ($fuel->vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $fuel->delete();
        return back()->with('success', 'Registro de abastecimento removido.');
    }
}
