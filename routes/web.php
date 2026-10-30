<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ObligationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (Visão Geral & Indicadores)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestão de Veículos (Garagem)
    Route::resource('vehicles', VehicleController::class);
    Route::post('/vehicles/{vehicle}/odometer', [VehicleController::class, 'updateMileage'])->middleware('throttle:60,1')->name('vehicles.odometer.update');
    Route::post('/vehicles/{vehicle}/simulate-data', [VehicleController::class, 'simulateTestData'])->middleware('throttle:5,1')->name('vehicles.simulate');

    // Manutenções Preventivas, Corretivas e Trocas de Óleo
    Route::resource('maintenances', MaintenanceController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Abastecimentos & Odômetro
    Route::get('/fuels', [FuelController::class, 'index'])->name('fuels.index');
    Route::post('/fuels', [FuelController::class, 'store'])->name('fuels.store');
    Route::delete('/fuels/{fuel}', [FuelController::class, 'destroy'])->name('fuels.destroy');

    // Obrigações, IPVA, Licenciamento e Seguros
    Route::get('/obligations', [ObligationController::class, 'index'])->name('obligations.index');
    Route::post('/obligations', [ObligationController::class, 'store'])->name('obligations.store');
    Route::patch('/obligations/{obligation}/pay', [ObligationController::class, 'markAsPaid'])->name('obligations.pay');
    Route::patch('/obligations/installments/{installment}/pay', [ObligationController::class, 'payInstallment'])->name('obligations.installments.pay');
    Route::patch('/obligations/installments/{installment}/reopen', [ObligationController::class, 'reopenInstallment'])->name('obligations.installments.reopen');
    Route::delete('/obligations/{obligation}', [ObligationController::class, 'destroy'])->name('obligations.destroy');

    // Relatórios e Prontuário para Venda
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/certificate/{vehicle}', [ReportController::class, 'printCertificate'])->name('reports.certificate');
    Route::get('/reports/export-csv/{vehicle}', [ReportController::class, 'exportCsv'])->name('reports.export-csv');

    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
