<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'academic_year_id',
        'term_number',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'term_number' => 'integer',
        'start_date'  => 'date',
        'end_date'    => 'date',
    ];

    /**
     * Get the academic year this term belongs to.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the fee structures assigned to this term.
     */
    public function feeStructures()
    {
        return $this->hasMany(ClassFeeStructure::class);
    }

    /**
     * Accessor to get a friendly name (e.g. "Term 1 - 2024")
     */
    public function getDisplayNameAttribute()
    {
        $year = $this->academicYear ? $this->academicYear->name : ''; // Assuming academic_years has a 'name' field
        return "Term {$this->term_number} " . ($year ? "- {$year}" : '');
    }
}