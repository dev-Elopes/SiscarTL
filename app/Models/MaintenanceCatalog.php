<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_type',
        'system_category',
        'item_name',
        'description',
        'default_interval_km',
        'default_interval_months',
        'severe_interval_km',
        'severe_interval_months',
        'is_critical',
    ];

    protected function casts(): array
    {
        return [
            'default_interval_km' => 'integer',
            'default_interval_months' => 'integer',
            'severe_interval_km' => 'integer',
            'severe_interval_months' => 'integer',
            'is_critical' => 'boolean',
        ];
    }

    public static function categoryLabels(): array
    {
        return [
            'engine_oil' => 'Motor & Lubrificação',
            'transmission' => 'Transmissão & Diferencial',
            'suspension_steering' => 'Suspensão & Direção',
            'brakes' => 'Sistema de Freios',
            'cooling_climate' => 'Arrefecimento & Clima',
            'electrical' => 'Elétrica & Eletrônica',
            'tires_wheels' => 'Pneus & Rodagem',
            'motorcycle_drive' => 'Transmissão Moto (Relação)',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabels()[$this->system_category] ?? ucfirst($this->system_category);
    }
}
