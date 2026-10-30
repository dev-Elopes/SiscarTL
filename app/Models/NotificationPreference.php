<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_alerts_enabled',
        'alert_km_before',
        'alert_days_before',
        'notify_overdue',
        'notify_monthly_odometer_reminder',
    ];

    protected function casts(): array
    {
        return [
            'email_alerts_enabled' => 'boolean',
            'alert_km_before' => 'integer',
            'alert_days_before' => 'array',
            'notify_overdue' => 'boolean',
            'notify_monthly_odometer_reminder' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
