<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleObligationInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_obligation_id',
        'installment_number',
        'total_installments',
        'due_date',
        'amount',
        'status',
        'paid_at',
        'amount_paid',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'installment_number' => 'integer',
            'total_installments' => 'integer',
            'due_date' => 'date',
            'paid_at' => 'date',
            'amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function obligation(): BelongsTo
    {
        return $this->belongsTo(VehicleObligation::class, 'vehicle_obligation_id');
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
                'label' => 'Paga',
                'bg' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            ],
            'overdue' => [
                'label' => 'Atrasada',
                'bg' => 'bg-rose-50 text-[#E11D48] border border-rose-200',
            ],
            default => [
                'label' => 'A Vencer',
                'bg' => 'bg-amber-50 text-amber-700 border border-amber-200',
            ],
        };
    }
}
