<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'registration_number',
        'model',
        'capacity',
        'status',
    ];

    /**
     * Get the driver (User) assigned to this vehicle.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Helper to get status badge colors
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'ACTIVE'      => 'success',
            'MAINTENANCE' => 'warning',
            'INACTIVE'    => 'secondary',
            default       => 'light',
        };
    }
}