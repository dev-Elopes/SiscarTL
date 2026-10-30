<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleObligation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'type',
        'title',
        'reference_year',
        'due_date',
        'amount',
        'status',
        'paid_at',
        'amount_paid',
        'installment_number',
        'total_installments',
        'receipt_image_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reference_year' => 'integer',
            'due_date' => 'date',
            'paid_at' => 'date',
            'amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'installment_number' => 'integer',
            'total_installments' => 'integer',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function installments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VehicleObligationInstallment::class, 'vehicle_obligation_id')->orderBy('installment_number');
    }

    public function getPaidInstallmentsCountAttribute(): int
    {
        return $this->installments()->where('status', 'paid')->count();
    }

    public function getTotalInstallmentsCountAttribute(): int
    {
        return $this->installments()->count() ?: ($this->total_installments ?: 1);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'ipva' => 'IPVA',
            'licensing' => 'Licenciamento Anual',
            'insurance' => 'Seguro do Veículo',
            'dpvat' => 'SPVAT / DPVAT',
            'fine' => 'Multa de Trânsito',
            'inspection' => 'Vistoria / Inspeção Veicular',
            default => ucfirst($this->type),
        };
    }

    public function getComputedStatusAttribute(): string
    {
        if ($this->status === 'paid') {
            return 'paid';
        }

        if (now()->startOfDay()->gt(Carbon::parse($this->due_date)->startOfDay())) {
            return 'overdue';
        }

        return 'pending';
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->computed_status) {
            'paid' => [
                'label' => 'Pago',
                'bg' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 border border-green-300 dark:border-green-800',
            ],
            'overdue' => [
                'label' => 'Atrasado',
                'bg' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 border border-red-300 dark:border-red-800',
            ],
            default => [
                'label' => 'A Vencer',
                'bg' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-800',
            ],
        };
    }
}
