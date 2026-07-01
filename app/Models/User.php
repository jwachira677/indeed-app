<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 
    'email', 
    'password',
    'role_id',
    'phone_number',
    'national_id',
    'member_id',
    'date_of_birth',
    'address',
    'status',
    'joined_at',
    'last_login_at'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'joined_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    // ==================== RELATIONSHIPS ====================
    
    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoans()
    {
        return $this->hasMany(Loan::class)->whereIn('status', ['approved', 'active']);
    }

    public function loanPayments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function guarantorLoans()
    {
        return $this->hasMany(LoanGuarantor::class);
    }

    public function approvedLoans()
    {
        return $this->hasMany(Loan::class, 'approved_by');
    }

    public function getTotalLoanBalanceAttribute(): float
    {
        return $this->loans()
            ->whereIn('status', ['approved', 'active'])
            ->get()
            ->sum(function ($loan) {
                return $loan->balance;
            });
    }

public function getGuaranteedAmountAttribute(): float
{
    return $this->guarantorLoans()
        ->whereIn('status', ['approved', 'active'])
        ->sum('amount_guaranteed');
}
    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    // ==================== ROLE CHECKING ====================
    
    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role?->slug === $roleSlug;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a member.
     */
    public function isMember(): bool
    {
        return $this->hasRole('member');
    }

    /**
     * Check if user is a guest.
     */
    public function isGuest(): bool
    {
        return $this->hasRole('guest');
    }

    // ==================== STATUS CHECKING ====================
    
    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if user is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    // ==================== SCOPES ====================
    
    /**
     * Scope a query to only include members.
     */
    public function scopeMembers($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('slug', 'member');
        });
    }

    /**
     * Scope a query to only include admins.
     */
    public function scopeAdmins($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('slug', 'admin');
        });
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to search for users.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('member_id', 'LIKE', "%{$search}%")
              ->orWhere('national_id', 'LIKE', "%{$search}%")
              ->orWhere('phone_number', 'LIKE', "%{$search}%");
        });
    }

    // ==================== ACCESSORS ====================
    
    /**
     * Get the status badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'pending' => 'warning',
            'suspended' => 'danger',
            'inactive' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get the full name (alias for name).
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper($word[0]);
        }
        return $initials;
    }

    /**
     * Get the avatar URL.
     */
    public function getAvatarAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . 
               '&background=0D6EFD&color=fff&size=100';
    }

    // ==================== BOOT METHODS ====================
    
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            // Auto-generate member ID for members
            if (empty($user->member_id) && $user->isMember()) {
                $user->member_id = 'MBR' . str_pad(
                    static::where('role_id', function($query) {
                        $query->select('id')->from('roles')->where('slug', 'member');
                    })->count() + 1, 
                    6, 
                    '0', 
                    STR_PAD_LEFT
                );
            }
            
            // Set joined date if not set
            if (empty($user->joined_at)) {
                $user->joined_at = now();
            }
            
            // Set default status if not set
            if (empty($user->status)) {
                $user->status = 'pending';
            }
        });
    }
}