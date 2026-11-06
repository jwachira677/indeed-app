<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportRoute extends Model
{
    protected $table = 'transport_routes';
    protected $fillable = ['name', 'amount', 'is_active'];
    protected $casts = ['amount' => 'decimal:2', 'is_active' => 'boolean'];
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}