<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'year_name',
        'start_date',
        'end_date',
        'is_current',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Get the terms for this academic year.
     */
    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    /**
     * Get the classes for this academic year.
     */
    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    /**
     * Scope a query to only include the current academic year.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Accessor to get a nice date range string.
     * e.g. "Jan 2024 - Dec 2024"
     */
    public function getDateRangeAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 'Dates not set';
        }
        
        return $this->start_date->format('M Y') . ' - ' . $this->end_date->format('M Y');
    }
}