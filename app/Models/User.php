<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function vehicles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function maintenanceRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function fuelRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FuelRecord::class);
    }

    public function notificationPreference(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(NotificationPreference::class);
    }
}
