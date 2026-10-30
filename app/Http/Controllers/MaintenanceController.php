<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceCatalog;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceRecordItem;
use App\Models\OdometerLog;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles()->get();

        $query = MaintenanceRecord::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->with(['vehicle', 'items'])
            ->orderByDesc('performed_at');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('system_category')) {
            $query->where('system_category', $request->system_category);
        }

        $records = $query->paginate(12)->withQueryString();
        $categories = MaintenanceCatalog::categoryLabels();

        return view('manutencoes.index', compact('records', 'vehicles', 'categories'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles()->with('schedules')->get();

        if ($vehicles->isEmpty()) {
            return redirect()->route('vehicles.create')
                ->with('warning', 'Cadastre um veículo primeiro para registrar manutenções.');
        }

        $selectedVehicleId = $request->get('vehicle_id', $vehicles->first()->id);
        $selectedScheduleId = $request->get('schedule_id');
        $preselectedType = $request->get('type', 'preventive');

        $categories = MaintenanceCatalog::categoryLabels();

        return view('manutencoes.create', compact('vehicles', 'selectedVehicleId', 'selectedScheduleId', 'preselectedType', 'categories'));
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            // Só aceita veículos pertencentes ao usuário logado
            'vehicle_id' => ['required', Rule::exists('vehicles', 'id')->where('user_id', $userId)->whereNull('deleted_at')],
            // O plano preventivo precisa pertencer ao MESMO veículo informado (evita IDOR)
            'schedule_id' => ['nullable', Rule::exists('vehicle_maintenance_schedules', 'id')->where('vehicle_id', $request->input('vehicle_id'))],
            'type' => 'required|in:oil_change,preventive,corrective',
            'system_category' => 'required|string|max:50',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:5000',
            'performed_at' => 'required|date',
            'mileage' => 'required|integer|min:0|max:9999999',
            'parts_cost' => 'nullable|numeric|min:0|max:99999999',
            'labor_cost' => 'nullable|numeric|min:0|max:99999999',
            'workshop_name' => 'nullable|string|max:100',
            'workshop_phone' => 'nullable|string|max:30',
            'technician_name' => 'nullable|string|max:100',
            'oil_viscosity' => 'nullable|string|max:50',
            'oil_brand' => 'nullable|string|max:50',
            'oil_liters' => 'nullable|numeric|min:0|max:99',
            'filters_replaced' => 'nullable|array|max:10',
            'filters_replaced.*' => 'string|max:30',
            'warranty_days' => 'nullable|integer|min:0|max:36500',
            'warranty_km' => 'nullable|integer|min:0|max:9999999',
            'receipt_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'items' => 'nullable|array|max:50',
            'items.*.item_name' => 'nullable|string|max:150',
            'items.*.quantity' => 'nullable|numeric|min:0.01|max:9999',
            'items.*.unit_price' => 'nullable|numeric|min:0|max:99999999',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        if ($vehicle->user_id !== $userId) {
            abort(403);
        }

        $partsCost = (float) ($validated['parts_cost'] ?? 0);
        $laborCost = (float) ($validated['labor_cost'] ?? 0);
        $totalCost = $partsCost + $laborCost;

        if ($request->hasFile('receipt_photo')) {
            $validated['receipt_photo_path'] = $request->file('receipt_photo')->store('receipts', 'public');
        }

        $record = MaintenanceRecord::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $request->user()->id,
            'schedule_id' => $validated['schedule_id'] ?? null,
            'type' => $validated['type'],
            'system_category' => $validated['system_category'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'performed_at' => $validated['performed_at'],
            'mileage' => $validated['mileage'],
            'parts_cost' => $partsCost,
            'labor_cost' => $laborCost,
            'total_cost' => $totalCost,
            'workshop_name' => $validated['workshop_name'] ?? null,
            'workshop_phone' => $validated['workshop_phone'] ?? null,
            'technician_name' => $validated['technician_name'] ?? null,
            'oil_viscosity' => $validated['oil_viscosity'] ?? null,
            'oil_brand' => $validated['oil_brand'] ?? null,
            'oil_liters' => $validated['oil_liters'] ?? null,
            'filters_replaced' => $validated['filters_replaced'] ?? null,
            'warranty_days' => $validated['warranty_days'] ?? null,
            'warranty_km' => $validated['warranty_km'] ?? null,
            'receipt_photo_path' => $validated['receipt_photo_path'] ?? null,
        ]);

        // Salvar peças detalhadas se informadas
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (!empty($item['item_name'])) {
                    $qty = (float) ($item['quantity'] ?? 1);
                    $price = (float) ($item['unit_price'] ?? 0);
                    MaintenanceRecordItem::create([
                        'maintenance_record_id' => $record->id,
                        'item_name' => $item['item_name'],
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'total_price' => $qty * $price,
                    ]);
                }
            }
        }

        // Atualizar odômetro do veículo se for maior
        if ($validated['mileage'] > $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $validated['mileage']]);
            OdometerLog::create([
                'vehicle_id' => $vehicle->id,
                'mileage' => $validated['mileage'],
                'recorded_at' => $validated['performed_at'],
                'source' => 'maintenance',
                'notes' => 'Registrado na manutenção: '.$record->title,
            ]);
        }

        // Se vinculado a um plano preventivo, recalcular próximo vencimento
        if (!empty($validated['schedule_id'])) {
            $schedule = VehicleMaintenanceSchedule::where('id', $validated['schedule_id'])
                ->where('vehicle_id', $vehicle->id)
                ->first();
            if ($schedule) {
                $schedule->last_performed_at = $validated['performed_at'];
                $schedule->last_performed_mileage = $validated['mileage'];
                $schedule->next_due_mileage = $schedule->interval_km ? ($validated['mileage'] + $schedule->interval_km) : null;
                $schedule->next_due_date = $schedule->interval_months ? Carbon::parse($validated['performed_at'])->addMonths($schedule->interval_months) : null;
                $schedule->save();
            }
        }

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Manutenção registrada no prontuário com sucesso!');
    }

    public function show(MaintenanceRecord $maintenance)
    {
        $vehicle = $maintenance->vehicle;
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $maintenance->load(['items', 'schedule']);
        return view('manutencoes.show', compact('maintenance', 'vehicle'));
    }

    public function destroy(MaintenanceRecord $maintenance)
    {
        $vehicle = $maintenance->vehicle;
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $maintenance->delete();
        return back()->with('success', 'Registro de manutenção excluído com sucesso.');
    }
}
