<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassFeeStructure extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'class_fee_structures';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'class_id',
        'term_id',
        'fee_type_id',
        'amount',
        'is_optional',
        'due_date',
        'notes',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'amount'      => 'decimal:2',
        'is_optional' => 'boolean',
        'due_date'    => 'date',
    ];


    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the term this fee structure belongs to.
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the fee type applied to this structure.
     */
    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
}