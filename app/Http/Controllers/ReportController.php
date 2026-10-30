<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles()->with(['maintenanceRecords', 'fuelRecords', 'obligations'])->get();

        $selectedVehicleId = $request->get('vehicle_id');
        $selectedVehicle = $selectedVehicleId ? $vehicles->firstWhere('id', $selectedVehicleId) : $vehicles->first();

        return view('relatorios.index', compact('vehicles', 'selectedVehicle'));
    }

    public function printCertificate(Vehicle $vehicle)
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $vehicle->load([
            'schedules',
            'maintenanceRecords.items',
            'odometerLogs',
        ]);

        return view('relatorios.certificate', compact('vehicle'));
    }

    public function exportCsv(Vehicle $vehicle): StreamedResponse
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $plate = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $vehicle->license_plate) ?: 'sem_placa';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="historico_veiculo_'.$plate.'_'.date('Ymd').'.csv"',
            'X-Content-Type-Options' => 'nosniff',
        ];

        return response()->stream(function () use ($vehicle) {
            // Impede CSV/Formula Injection: células de texto iniciadas por = + - @ \t \r
            // seriam executadas como fórmula ao abrir no Excel/LibreOffice.
            $safe = function ($value) {
                if (!is_string($value) || $value === '') {
                    return $value;
                }
                return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
            };

            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // Cabeçalho Veículo
            fputcsv($handle, ['PRONTUÁRIO DE MANUTENÇÃO VEICULAR']);
            fputcsv($handle, ['Veículo', $safe($vehicle->nickname)]);
            fputcsv($handle, ['Marca/Modelo', $safe($vehicle->brand . ' ' . $vehicle->model)]);
            fputcsv($handle, ['Placa', $safe($vehicle->license_plate)]);
            fputcsv($handle, ['Ano', $vehicle->year_manufacture . '/' . $vehicle->year_model]);
            fputcsv($handle, ['Odômetro Atual', $vehicle->current_mileage . ' km']);
            fputcsv($handle, []);

            // Histórico de Manutenções
            fputcsv($handle, ['HISTÓRICO DE MANUTENÇÕES REALIZADAS']);
            fputcsv($handle, ['Data', 'KM', 'Tipo', 'Categoria', 'Título / Descrição', 'Oficina', 'Garantia', 'Peças (R$)', 'Mão de Obra (R$)', 'Total (R$)']);

            foreach ($vehicle->maintenanceRecords as $rec) {
                fputcsv($handle, [
                    $rec->performed_at->format('d/m/Y'),
                    $rec->mileage,
                    $rec->type_label,
                    $rec->category_label,
                    $safe($rec->title),
                    $safe($rec->workshop_name),
                    $rec->warranty_days ? $rec->warranty_days . ' dias' : '-',
                    number_format($rec->parts_cost, 2, ',', '.'),
                    number_format($rec->labor_cost, 2, ',', '.'),
                    number_format($rec->total_cost, 2, ',', '.'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
