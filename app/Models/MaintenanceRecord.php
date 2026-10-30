<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'schedule_id',
        'type',
        'system_category',
        'title',
        'description',
        'performed_at',
        'mileage',
        'parts_cost',
        'labor_cost',
        'total_cost',
        'workshop_name',
        'workshop_phone',
        'technician_name',
        'oil_viscosity',
        'oil_brand',
        'oil_liters',
        'filters_replaced',
        'warranty_days',
        'warranty_km',
        'receipt_photo_path',
        'invoice_url',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'date',
            'mileage' => 'integer',
            'parts_cost' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'oil_liters' => 'decimal:2',
            'filters_replaced' => 'array',
            'warranty_days' => 'integer',
            'warranty_km' => 'integer',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(VehicleMaintenanceSchedule::class, 'schedule_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MaintenanceRecordItem::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'oil_change' => 'Troca de Óleo & Filtros',
            'preventive' => 'Manutenção Preventiva',
            'corrective' => 'Manutenção Corretiva / Reparo',
            default => ucfirst($this->type),
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return MaintenanceCatalog::categoryLabels()[$this->system_category] ?? ucfirst($this->system_category);
    }
}
