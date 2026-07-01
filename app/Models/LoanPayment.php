<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'user_id',
        'received_by',
        'receipt_number',
        'amount',
        'balance_before',
        'balance_after',
        'penalty_charged',
        'payment_method',
        'transaction_id',
        'payment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'penalty_charged' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // ==================== ACCESSORS ====================
    
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    // ==================== BOOT METHODS ====================
    
    protected static function booted(): void
    {
        static::creating(function ($payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = 'LPMT-' . date('Y') . '-' . Str::random(8);
            }
            
            if (empty($payment->payment_date)) {
                $payment->payment_date = now();
            }
        });
    }
}