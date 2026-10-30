<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleMaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'catalog_id',
        'system_category',
        'item_name',
        'interval_km',
        'interval_months',
        'last_performed_at',
        'last_performed_mileage',
        'next_due_mileage',
        'next_due_date',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'interval_km' => 'integer',
            'interval_months' => 'integer',
            'last_performed_at' => 'date',
            'last_performed_mileage' => 'integer',
            'next_due_mileage' => 'integer',
            'next_due_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(MaintenanceCatalog::class, 'catalog_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class, 'schedule_id');
    }

    /**
     * Recalcula os próximos vencimentos com base na última execução ou odômetro inicial
     */
    public function recalculateDue(): void
    {
        $baseKm = $this->last_performed_mileage ?? $this->vehicle->current_mileage;
        $baseDate = $this->last_performed_at ?? Carbon::parse($this->vehicle->created_at);

        if ($this->interval_km) {
            $this->next_due_mileage = $baseKm + $this->interval_km;
        }

        if ($this->interval_months) {
            $this->next_due_date = (clone $baseDate)->addMonths($this->interval_months);
        }

        $this->save();
    }

    /**
     * Calcula o status do item: 'overdue' (vencido), 'warning' (atenção/próximo), 'ok' (em dia)
     */
    public function getNextDueKmAttribute(): ?int
    {
        return $this->next_due_mileage;
    }

    public function getStatusAttribute(): string
    {
        $vehicle = $this->vehicle;
        $currentKm = max((int) $vehicle->current_mileage, (int) $vehicle->estimated_mileage);
        $today = now()->startOfDay();

        $kmOverdue = $this->next_due_mileage && ($currentKm >= $this->next_due_mileage);
        $dateOverdue = $this->next_due_date && ($today->gt(Carbon::parse($this->next_due_date)->startOfDay()));

        if ($kmOverdue || $dateOverdue) {
            return 'overdue';
        }

        $kmWarning = $this->next_due_mileage && (($this->next_due_mileage - $currentKm) <= 2000);
        $daysUntilDue = $this->next_due_date ? $today->diffInDays(Carbon::parse($this->next_due_date)->startOfDay(), false) : null;
        $dateWarning = ($daysUntilDue !== null && $daysUntilDue <= 30 && $daysUntilDue >= 0);

        if ($kmWarning || $dateWarning) {
            return 'warning';
        }

        return 'ok';
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'overdue' => [
                'label' => 'Vencido',
                'bg' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400 border border-red-300 dark:border-red-800',
                'icon' => 'exclamation-circle',
            ],
            'warning' => [
                'label' => 'Atenção (Próximo)',
                'bg' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400 border border-yellow-300 dark:border-yellow-800',
                'icon' => 'clock',
            ],
            default => [
                'label' => 'Em dia',
                'bg' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400 border border-green-300 dark:border-green-800',
                'icon' => 'check-circle',
            ],
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return MaintenanceCatalog::categoryLabels()[$this->system_category] ?? ucfirst($this->system_category);
    }
}
