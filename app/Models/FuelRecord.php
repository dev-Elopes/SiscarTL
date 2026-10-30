<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'date',
        'mileage',
        'fuel_type',
        'liters',
        'price_per_liter',
        'total_cost',
        'is_full_tank',
        'km_per_liter',
        'gas_station',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'mileage' => 'integer',
            'liters' => 'decimal:2',
            'price_per_liter' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'is_full_tank' => 'boolean',
            'km_per_liter' => 'decimal:2',
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

    public function getFuelLabelAttribute(): string
    {
        return match ($this->fuel_type) {
            'gasoline' => 'Gasolina',
            'ethanol' => 'Etanol',
            'diesel' => 'Diesel',
            'cng' => 'GNV',
            default => ucfirst($this->fuel_type),
        };
    }
}
