<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'interest_rate',
        'max_amount',
        'is_active',
        'description',
    ];

    protected $casts = [
        'interest_rate' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class, 'loan_type_id');
    }

    public function durations()
    {
        return $this->hasMany(LoanDuration::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedInterestAttribute(): string
    {
        return number_format($this->interest_rate, 2) . '%';
    }

    public function getFormattedMaxAmountAttribute(): string
    {
        return 'KES ' . number_format($this->max_amount, 2);
    }

    public function getDurationForAmount($amount): int
    {
        if ($this->slug === 'instant') {
            return 7; // Instant loans are always 7 days
        }

        // For ordinary loans, get the tiered duration
        $duration = $this->durations()
            ->where('min_amount', '<=', $amount)
            ->where('max_amount', '>=', $amount)
            ->where('is_active', true)
            ->first();

        return $duration ? $duration->duration_days : 30;
    }

    public function getDurationTiers()
    {
        if ($this->slug === 'ordinary') {
            return $this->durations()->where('is_active', true)->orderBy('min_amount')->get();
        }
        return collect();
    }

    public function getDurationRangeLabel($amount): string
    {
        if ($this->slug === 'instant') {
            return '7 days (Instant)';
        }

        $duration = $this->durations()
            ->where('min_amount', '<=', $amount)
            ->where('max_amount', '>=', $amount)
            ->where('is_active', true)
            ->first();

        return $duration ? $duration->duration_days . ' days' : '30 days (Default)';
    }

    public static function getDefaultTypes()
    {
        return self::active()->get();
    }
}