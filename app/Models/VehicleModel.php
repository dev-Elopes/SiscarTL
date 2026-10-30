<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'name',
        'type',
        'category',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(VehicleBrand::class, 'brand_id');
    }
}
