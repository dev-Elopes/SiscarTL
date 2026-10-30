<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'logo_url',
    ];

    public function models(): HasMany
    {
        return $this->hasMany(VehicleModel::class, 'brand_id');
    }
}
