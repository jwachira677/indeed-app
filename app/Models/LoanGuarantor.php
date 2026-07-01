<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanGuarantor extends Model
{
    use HasFactory;

    protected $table = 'loan_guarantors';

    protected $fillable = [
        'loan_id',
        'user_id',
        'approved_by',
        'amount_guaranteed',
        'amount_paid',
        'relationship',
        'phone_number',
        'email',
        'address',
        'status',
        'guaranteed_at',
        'approved_at',
        'released_at',
    ];

    protected $casts = [
        'amount_guaranteed' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'guaranteed_at' => 'datetime',
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==================== ACCESSORS ====================
    
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'active' => 'primary',
            'released' => 'info',
            'defaulted' => 'danger',
            default => 'secondary',
        };
    }

    public function getBalanceAttribute(): float
    {
        return $this->amount_guaranteed - $this->amount_paid;
    }

    public function getProgressAttribute(): float
    {
        if ($this->amount_guaranteed == 0) {
            return 0;
        }
        return round(($this->amount_paid / $this->amount_guaranteed) * 100, 2);
    }

    // ==================== SCOPES ====================
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'active']);
    }

    // ==================== BOOT METHODS ====================
    
    protected static function booted(): void
    {
        static::creating(function ($guarantor) {
            if (empty($guarantor->guaranteed_at)) {
                $guarantor->guaranteed_at = now();
            }
        });
    }

    // ==================== HELPER METHODS ====================
    
    public function approve($approverId)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
        
        // Check if loan is now fully guaranteed
        $this->loan->refresh();
        if ($this->loan->isFullyGuaranteed && $this->loan->status === 'pending') {
            $this->loan->update(['status' => 'approved']);
        }
        
        return $this;
    }

    public function release()
    {
        $this->update([
            'status' => 'released',
            'released_at' => now(),
        ]);
        
        return $this;
    }
}