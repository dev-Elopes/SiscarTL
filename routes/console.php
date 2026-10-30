<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('vehicles:clear', function () {
    \App\Models\MaintenanceRecordItem::query()->delete();
    \App\Models\MaintenanceRecord::query()->delete();
    \App\Models\VehicleMaintenanceSchedule::query()->delete();
    \App\Models\OdometerLog::query()->delete();
    \App\Models\FuelRecord::query()->delete();
    \App\Models\VehicleObligation::query()->delete();
    \App\Models\Vehicle::withTrashed()->forceDelete();

    $this->info('Todos os veículos e históricos foram apagados com sucesso! O banco está pronto para novos cadastros.');
})->purpose('Apaga todos os veículos e históricos cadastrados mantendo usuários e catálogos');

Artisan::command('vehicles:status', function () {
    $this->line('Veículos no banco: ' . \App\Models\Vehicle::withTrashed()->count());
    $this->line('Usuários ativos: ' . \App\Models\User::count());
    $this->line('Marcas no catálogo: ' . \App\Models\VehicleBrand::count());
    $this->line('Modelos no catálogo: ' . \App\Models\VehicleModel::count());
    $this->line('Itens do catálogo preventivo: ' . \App\Models\MaintenanceCatalog::count());
})->purpose('Exibe o resumo do banco de dados');

Artisan::command('vehicles:inspect', function () {
    $vehicles = \App\Models\Vehicle::with('schedules')->get();
    foreach ($vehicles as $v) {
        $this->line("=== Veículo: {$v->nickname} (ID: {$v->id}) ===");
        $this->line("KM Atual: {$v->current_mileage}");
        $this->line("Estimated Mileage: {$v->estimated_mileage}");
        $this->line("Uso: {$v->usage_condition}");
        foreach ($v->schedules as $s) {
            $currentKm = $v->estimated_mileage ?? $v->current_mileage;
            $kmOverdue = $s->next_due_mileage && ($currentKm >= $s->next_due_mileage);
            $this->line("  [{$s->system_category}] {$s->item_name}:");
            $this->line("     Última: {$s->last_performed_mileage} km | Próxima: {$s->next_due_mileage} km | Intervalo: {$s->interval_km} km");
            $this->line("     currentKm evaluated: {$currentKm} | kmOverdue: " . ($kmOverdue ? 'TRUE' : 'FALSE') . " | Status: {$s->status}");
        }
    }
})->purpose('Inspeciona os veículos e seus cronogramas');
