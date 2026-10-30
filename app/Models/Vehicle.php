<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'nickname',
        'brand',
        'model',
        'year_manufacture',
        'year_model',
        'fuel_type',
        'current_mileage',
        'average_monthly_mileage',
        'expected_km_per_liter',
        'tank_capacity',
        'license_plate',
        'color',
        'chassis',
        'renavam',
        'transmission_type',
        'secondary_drive_type',
        'usage_condition',
        'status',
        'photo_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'year_manufacture' => 'integer',
            'year_model' => 'integer',
            'current_mileage' => 'integer',
            'average_monthly_mileage' => 'integer',
            'expected_km_per_liter' => 'decimal:2',
            'tank_capacity' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function odometerLogs(): HasMany
    {
        return $this->hasMany(OdometerLog::class)->orderByDesc('recorded_at')->orderByDesc('id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(VehicleMaintenanceSchedule::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class)->orderByDesc('performed_at')->orderByDesc('id');
    }

    public function fuelRecords(): HasMany
    {
        return $this->hasMany(FuelRecord::class)->orderByDesc('date')->orderByDesc('id');
    }

    public function obligations(): HasMany
    {
        return $this->hasMany(VehicleObligation::class)->orderBy('due_date');
    }

    /**
     * Projeção linear de quilometragem estimada com base na média mensal
     */
    public function getEstimatedMileageAttribute(): int
    {
        $lastLog = $this->odometerLogs()->latest('recorded_at')->latest('id')->first();
        $baseKm = max((int) $this->current_mileage, (int) ($lastLog ? $lastLog->mileage : 0));
        $baseDate = $lastLog ? Carbon::parse($lastLog->recorded_at) : Carbon::parse($this->updated_at ?? $this->created_at);

        $daysDiff = max(0, (int) now()->diffInDays($baseDate));
        $dailyKm = ($this->average_monthly_mileage ?: 1000) / 30;

        return max((int) $this->current_mileage, (int) round($baseKm + ($daysDiff * $dailyKm)));
    }

    /**
     * Média de quilometragem diária estimada
     */
    public function getAverageDailyKmAttribute(): int
    {
        $monthly = $this->average_monthly_mileage ?: 1000;
        return (int) round($monthly / 30);
    }

    /**
     * Rótulo formatado para o tipo de combustível
     */
    public function getFuelTypeLabelAttribute(): string
    {
        return match ($this->fuel_type) {
            'gasoline' => 'Gasolina',
            'ethanol' => 'Etanol',
            'flex' => 'Flex (Gasolina/Etanol)',
            'diesel' => 'Diesel',
            'cng' => 'GNV',
            'electric' => 'Elétrico',
            'hybrid' => 'Híbrido',
            default => ucfirst($this->fuel_type),
        };
    }

    /**
     * Quantidade de alertas críticos ou de atenção
     */
    public function getAlertsSummaryAttribute(): array
    {
        $schedules = $this->schedules()->where('is_active', true)->get();
        $overdue = 0;
        $warning = 0;

        foreach ($schedules as $sched) {
            if ($sched->status === 'overdue') {
                $overdue++;
            } elseif ($sched->status === 'warning') {
                $warning++;
            }
        }

        return [
            'overdue' => $overdue,
            'warning' => $warning,
            'total' => $overdue + $warning,
        ];
    }
}
