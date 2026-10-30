<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdometerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'mileage',
        'recorded_at',
        'source',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
            'mileage' => 'integer',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
