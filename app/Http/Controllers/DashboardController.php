<?php

namespace App\Http\Controllers;

use App\Models\FuelRecord;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceSchedule;
use App\Models\VehicleObligation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles()->with(['schedules', 'odometerLogs'])->get();

        $vehicleId = $request->get('vehicle_id');
        $selectedVehicle = $vehicleId ? $vehicles->firstWhere('id', $vehicleId) : $vehicles->first();

        // Alertas globais ou do veículo selecionado
        $schedulesQuery = VehicleMaintenanceSchedule::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->where('is_active', true)
            ->with(['vehicle']);

        if ($selectedVehicle) {
            $schedulesQuery->where('vehicle_id', $selectedVehicle->id);
        }

        $allSchedules = $schedulesQuery->get();

        $overdueSchedules = $allSchedules->filter(fn ($s) => $s->status === 'overdue')->values();
        $warningSchedules = $allSchedules->filter(fn ($s) => $s->status === 'warning')->values();
        $okSchedules = $allSchedules->filter(fn ($s) => $s->status === 'ok')->values();

        // Próximas manutenções agendadas / estimadas (Estilo Appointments do Figma)
        $upcomingAppointments = $allSchedules->filter(fn ($s) => in_array($s->status, ['overdue', 'warning']))
            ->sortBy(function ($item) {
                return $item->next_due_date ? Carbon::parse($item->next_due_date)->timestamp : PHP_INT_MAX;
            })
            ->take(6);

        // Despesas do mês
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $vehicleIds = $selectedVehicle ? [$selectedVehicle->id] : $vehicles->pluck('id')->toArray();

        $monthlyMaintenanceCost = MaintenanceRecord::whereIn('vehicle_id', $vehicleIds)
            ->whereBetween('performed_at', [$startOfMonth, $endOfMonth])
            ->sum('total_cost');

        $monthlyFuelCost = FuelRecord::whereIn('vehicle_id', $vehicleIds)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_cost');

        $monthlyObligationsCost = VehicleObligation::whereIn('vehicle_id', $vehicleIds)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount_paid');

        $totalMonthlyCost = $monthlyMaintenanceCost + $monthlyFuelCost + $monthlyObligationsCost;

        // Histórico recente (Timeline)
        $recentMaintenances = MaintenanceRecord::whereIn('vehicle_id', $vehicleIds)
            ->with('vehicle')
            ->orderByDesc('performed_at')
            ->take(5)
            ->get();

        // Saúde por subsistema mecânico (Inspirado nos esquemas automotivos)
        $subsystems = [
            'engine_oil' => ['name' => 'Motor & Lubrificação', 'icon' => 'cog', 'status' => 'ok', 'total' => 0, 'alerts' => 0],
            'transmission' => ['name' => 'Transmissão & Diferencial', 'icon' => 'cube-transparent', 'status' => 'ok', 'total' => 0, 'alerts' => 0],
            'suspension_steering' => ['name' => 'Suspensão & Direção', 'icon' => 'arrows-up-down', 'status' => 'ok', 'total' => 0, 'alerts' => 0],
            'brakes' => ['name' => 'Sistema de Freios', 'icon' => 'shield-check', 'status' => 'ok', 'total' => 0, 'alerts' => 0],
            'cooling_climate' => ['name' => 'Arrefecimento & Clima', 'icon' => 'sparkles', 'status' => 'ok', 'total' => 0, 'alerts' => 0],
        ];

        foreach ($allSchedules as $sched) {
            $cat = $sched->system_category;
            if (isset($subsystems[$cat])) {
                $subsystems[$cat]['total']++;
                if (in_array($sched->status, ['overdue', 'warning'])) {
                    $subsystems[$cat]['alerts']++;
                    $subsystems[$cat]['status'] = $sched->status === 'overdue' ? 'overdue' : 'warning';
                }
            }
        }

        // Obrigações pendentes ou vencidas
        $pendingObligations = VehicleObligation::whereIn('vehicle_id', $vehicleIds)
            ->where('status', '!=', 'paid')
            ->orderBy('due_date')
            ->take(4)
            ->get();

        return view('dashboard', compact(
            'vehicles',
            'selectedVehicle',
            'allSchedules',
            'overdueSchedules',
            'warningSchedules',
            'okSchedules',
            'upcomingAppointments',
            'monthlyMaintenanceCost',
            'monthlyFuelCost',
            'monthlyObligationsCost',
            'totalMonthlyCost',
            'recentMaintenances',
            'subsystems',
            'pendingObligations'
        ));
    }
}
