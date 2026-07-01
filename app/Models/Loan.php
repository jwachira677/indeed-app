<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'approved_by',
        'loan_number',
        'loan_type',
        'amount_taken',
        'amount_due',
        'interest_rate',
        'interest_amount',
        'total_amount_payable',
        'date_taken',
        'date_due',
        'date_paid',
        'payment_frequency',
        'installment_amount',
        'installments_count',
        'installments_paid',
        'status',
        'purpose',
        'notes',
        'approved_at',
    ];

    protected $casts = [
        'amount_taken' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount_payable' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'date_taken' => 'datetime',
        'date_due' => 'datetime',
        'date_paid' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function guarantors()
    {
        return $this->hasMany(LoanGuarantor::class);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    // ==================== ACCESSORS ====================
    
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'active' => 'primary',
            'completed' => 'success',
            'defaulted' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount_taken, 2);
    }

    public function getBalanceAttribute(): float
    {
        $totalPaid = $this->payments()->where('status', 'confirmed')->sum('amount');
        return max(0, $this->total_amount_payable - $totalPaid);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->where('status', 'confirmed')->sum('amount');
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->total_amount_payable == 0) {
            return 0;
        }
        return round(($this->total_paid / $this->total_amount_payable) * 100, 2);
    }

    public function getGuarantorsTotalAttribute(): float
    {
        return $this->guarantors()->where('status', 'approved')->sum('amount_guaranteed');
    }

    public function getIsFullyGuaranteedAttribute(): bool
    {
        return $this->guarantors_total >= $this->amount_taken;
    }

    // ==================== SCOPES ====================
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
                     ->where('date_due', '<', now());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ==================== BOOT METHODS ====================
    
    protected static function booted(): void
    {
        static::creating(function ($loan) {
            if (empty($loan->loan_number)) {
                $loan->loan_number = 'LN-' . date('Y') . '-' . Str::random(8);
            }
            
            if (empty($loan->date_taken)) {
                $loan->date_taken = now();
            }
            
            // Calculate interest
            if ($loan->interest_rate > 0) {
                $loan->interest_amount = ($loan->amount_taken * $loan->interest_rate) / 100;
                $loan->total_amount_payable = $loan->amount_taken + $loan->interest_amount;
            } else {
                $loan->total_amount_payable = $loan->amount_taken;
            }
            
            // Calculate installment amount if not set
            if ($loan->installments_count > 0 && empty($loan->installment_amount)) {
                $loan->installment_amount = $loan->total_amount_payable / $loan->installments_count;
            }
        });
    }

    // ==================== HELPER METHODS ====================
    
    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->date_due < now();
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending' && $this->isFullyGuaranteed;
    }

    public function recordPayment($amount, $paymentMethod, $transactionId = null, $notes = null)
    {
        $balanceBefore = $this->balance;
        $balanceAfter = max(0, $balanceBefore - $amount);

        $payment = $this->payments()->create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
            'payment_date' => now(),
            'notes' => $notes,
            'status' => 'confirmed',
        ]);

        // Update loan status if completed
        if ($balanceAfter == 0) {
            $this->update([
                'status' => 'completed',
                'date_paid' => now(),
            ]);
        }

        return $payment;
    }
}