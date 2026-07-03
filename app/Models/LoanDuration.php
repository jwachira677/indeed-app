<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanDuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'min_amount',
        'max_amount',
        'duration_days',
        'is_active',
        'description',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getDurationForAmount($amount, $loanTypeId)
    {
        $duration = self::where('loan_type_id', $loanTypeId)
            ->where('min_amount', '<=', $amount)
            ->where('max_amount', '>=', $amount)
            ->where('is_active', true)
            ->first();

        return $duration ? $duration->duration_days : 30; // Default to 30 days
    }

    public function getRangeLabelAttribute(): string
    {
        return 'KES ' . number_format($this->min_amount, 0) . ' - ' . number_format($this->max_amount, 0);
    }
}