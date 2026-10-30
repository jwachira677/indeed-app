<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fee_types';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'billing_frequency',
        'is_optional',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_optional' => 'boolean',
        'is_active'   => 'boolean',
    ];

    /**
     * Billing Frequency Constants
     */
    const FREQ_PER_TERM  = 'PER_TERM';
    const FREQ_PER_MONTH = 'PER_MONTH';
    const FREQ_PER_YEAR  = 'PER_YEAR';
    const FREQ_ONE_OFF   = 'ONE_OFF';

    /**
     * Get the available billing frequencies.
     */
    public static function getBillingFrequencies(): array
    {
        return [
            self::FREQ_PER_TERM  => 'Per Term',
            self::FREQ_PER_MONTH => 'Per Month',
            self::FREQ_PER_YEAR  => 'Per Year',
            self::FREQ_ONE_OFF   => 'One-off',
        ];
    }
}