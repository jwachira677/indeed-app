<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'approved_by',
    'receipt_number',
    'amount',
    'opening_balance',
    'is_carried_forward',
    'carried_forward_amount',
    'total_savings_to_date',
    'balance_before',
    'balance_after',
    'month',
    'year',
    'financial_year',
    'payment_method',
    'transaction_id',
    'notes',
    'status',
    'saved_date',
    'approved_at',
])]
class Saving extends Model
{
    use HasFactory;

    /**
     * Sentinel value for the "month" column representing the opening
     * balance / carried-forward entry at the start of a financial year.
     */
    public const MONTH_OPENING_BALANCE = 0;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'opening_balance' => 'decimal:2',
            'carried_forward_amount' => 'decimal:2',
            'total_savings_to_date' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'saved_date' => 'datetime',
            'approved_at' => 'datetime',
            'month' => 'integer',
            'year' => 'integer',
            'is_carried_forward' => 'boolean',
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

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCarriedForward($query)
    {
        return $query->where('is_carried_forward', true);
    }

    public function scopeNotCarriedForward($query)
    {
        return $query->where('is_carried_forward', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForFinancialYear($query, $year)
    {
        return $query->where('financial_year', $year);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeForCurrentFinancialYear($query)
    {
        return $query->where('financial_year', self::getCurrentFinancialYear());
    }

    // ==================== ACCESSORS ====================

    public function getMonthNameAttribute(): string
    {
        if ($this->month === self::MONTH_OPENING_BALANCE) {
            return 'Opening Balance';
        }

        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2);
    }

    public function getFormattedOpeningBalanceAttribute(): string
    {
        return number_format($this->opening_balance, 2);
    }

    public function getFormattedCarriedForwardAttribute(): string
    {
        return number_format($this->carried_forward_amount, 2);
    }

    // ==================== BOOT METHODS ====================

    protected static function booted(): void
    {
        static::creating(function ($saving) {
            // Generate receipt number
            if (empty($saving->receipt_number)) {
                $saving->receipt_number = 'RCP-' . date('Y') . '-' . Str::random(8);
            }

            // Set financial year
            if (empty($saving->financial_year)) {
                $saving->financial_year = self::getFinancialYear($saving->year ?? date('Y'), $saving->month ?? date('n'));
            }

            // Set saved date if not set
            if (empty($saving->saved_date)) {
                $saving->saved_date = now();
            }

            // Calculate total savings to date, scoped to this financial year
            // so prior-year deposits don't bleed into the running total.
            if (! $saving->is_carried_forward) {
                $saving->total_savings_to_date = $saving->opening_balance +
                    Saving::where('user_id', $saving->user_id)
                        ->where('financial_year', $saving->financial_year)
                        ->where('status', 'approved')
                        ->notCarriedForward()
                        ->sum('amount');
            }
        });
    }

    // ==================== STATIC HELPERS ====================

    /**
     * Get financial year for a given month and year.
     *
     * The financial year start month is configurable via
     * config('savings.financial_year_start_month'), defaulting to
     * December (12). E.g. with a Dec start, month=12/year=2025 and
     * month=1/year=2026 both resolve to "2025/2026".
     *
     * Month 0 (opening balance / carried forward) always belongs to the
     * cycle beginning in $year, i.e. the same financial year as the
     * start month itself.
     */
    public static function getFinancialYear($year, $month): string
    {
        $startMonth = config('savings.financial_year_start_month', 12);

        if ($month === self::MONTH_OPENING_BALANCE || $month >= $startMonth) {
            return $year . '/' . ($year + 1);
        }

        return ($year - 1) . '/' . $year;
    }

    /**
     * Get current financial year.
     */
    public static function getCurrentFinancialYear(): string
    {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        return self::getFinancialYear($currentYear, $currentMonth);
    }

    /**
     * Get the calendar month numbers in financial-year order, e.g. with a
     * Dec start: [12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11].
     * Useful for building month dropdowns/reports in fiscal order rather
     * than calendar order.
     */
    public static function getFinancialYearMonthOrder(): array
    {
        $startMonth = config('savings.financial_year_start_month', 12);

        return array_merge(range($startMonth, 12), range(1, $startMonth - 1));
    }

    /**
     * Get member's opening balance for a financial year.
     */
    public static function getMemberOpeningBalance($userId, $financialYear): float
    {
        $carriedForward = self::where('user_id', $userId)
            ->where('financial_year', $financialYear)
            ->where('is_carried_forward', true)
            ->where('status', 'approved')
            ->first();

        return $carriedForward ? (float) $carriedForward->amount : 0.0;
    }

    /**
     * Get member's total savings for a financial year (excluding carried forward).
     */
    public static function getMemberYearlySavings($userId, $financialYear): float
    {
        return (float) self::where('user_id', $userId)
            ->where('financial_year', $financialYear)
            ->where('status', 'approved')
            ->notCarriedForward()
            ->sum('amount');
    }

    /**
     * Get member's total savings including carried forward.
     */
    public static function getMemberTotalWithOpening($userId, $financialYear): float
    {
        $opening = self::getMemberOpeningBalance($userId, $financialYear);
        $savings = self::getMemberYearlySavings($userId, $financialYear);

        return $opening + $savings;
    }

    /**
     * Get all members with their opening balances for a financial year.
     */
    public static function getMembersOpeningBalances($financialYear): array
    {
        return self::where('financial_year', $financialYear)
            ->where('is_carried_forward', true)
            ->where('status', 'approved')
            ->with('user')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->user_id => (float) $item->amount];
            })
            ->toArray();
    }
}