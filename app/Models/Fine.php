<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'approved_by',
    'fine_number',
    'amount',
    'reason',
    'description',
    'date_paid',
    'due_date',
    'amount_paid',
    'balance',
    'payment_method',
    'transaction_id',
    'status',
    'issued_date',
    'approved_at',
])]
class Fine extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'balance' => 'decimal:2',
            'date_paid' => 'date',
            'due_date' => 'date',
            'issued_date' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==================== SCOPES ====================
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'pending')
                  ->where('due_date', '<', now());
            });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'overdue']);
    }

    // ==================== ACCESSORS ====================
    
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'paid' => 'success',
            'pending' => 'warning',
            'overdue' => 'danger',
            'waived' => 'secondary',
            'partially_paid' => 'info',
            default => 'secondary',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'overdue' || 
               ($this->status === 'pending' && $this->due_date && $this->due_date < now());
    }

    public function getDaysOverdueAttribute(): ?int
    {
        if (!$this->is_overdue || !$this->due_date) {
            return null;
        }
        return now()->diffInDays($this->due_date);
    }

    // ==================== BOOT METHODS ====================
    
    protected static function booted(): void
    {
        static::creating(function ($fine) {
            // Generate fine number
            if (empty($fine->fine_number)) {
                $fine->fine_number = 'FIN-' . date('Y') . '-' . Str::random(8);
            }

            // Set issued date
            if (empty($fine->issued_date)) {
                $fine->issued_date = now();
            }

            // Calculate balance
            if (empty($fine->balance)) {
                $fine->balance = $fine->amount - ($fine->amount_paid ?? 0);
            }

            // Auto-set status to overdue if due date passed
            if ($fine->due_date && $fine->due_date < now() && $fine->status === 'pending') {
                $fine->status = 'overdue';
            }
        });

        static::updating(function ($fine) {
            // Recalculate balance when amount_paid changes
            if ($fine->isDirty('amount_paid')) {
                $fine->balance = $fine->amount - $fine->amount_paid;
                
                // Update status based on payment
                if ($fine->balance <= 0) {
                    $fine->status = 'paid';
                    if (empty($fine->date_paid)) {
                        $fine->date_paid = now();
                    }
                } elseif ($fine->amount_paid > 0) {
                    $fine->status = 'partially_paid';
                }
            }

            // Auto-set status to overdue if due date passed
            if ($fine->due_date && $fine->due_date < now() && in_array($fine->status, ['pending', 'partially_paid'])) {
                $fine->status = 'overdue';
            }
        });
    }

    // ==================== HELPER METHODS ====================
    
    /**
     * Record a payment against this fine
     */
    public function recordPayment($amount, $paymentMethod = null, $transactionId = null): void
    {
        $this->amount_paid += $amount;
        $this->balance = $this->amount - $this->amount_paid;
        
        if ($paymentMethod) {
            $this->payment_method = $paymentMethod;
        }
        
        if ($transactionId) {
            $this->transaction_id = $transactionId;
        }
        
        if ($this->balance <= 0) {
            $this->status = 'paid';
            $this->date_paid = now();
        } else {
            $this->status = 'partially_paid';
        }
        
        $this->save();
    }

    /**
     * Waive the fine
     */
    public function waive($approvedBy = null): void
    {
        $this->status = 'waived';
        $this->approved_by = $approvedBy ?? auth()->id();
        $this->approved_at = now();
        $this->save();
    }

    /**
     * Get total unpaid fines for a user
     */
    public static function getUserTotalUnpaid($userId): float
    {
        return self::where('user_id', $userId)
            ->whereIn('status', ['pending', 'overdue', 'partially_paid'])
            ->sum('balance');
    }

    /**
     * Get total fines for a user
     */
    public static function getUserTotalFines($userId): float
    {
        return self::where('user_id', $userId)
            ->whereIn('status', ['paid', 'partially_paid', 'overdue'])
            ->sum('amount');
    }
}