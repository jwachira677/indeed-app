<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'mother_name',
        'father_name',
        'guardian_name',
        'date_of_birth',
        'gender',
        'transport_route_id',
    ];
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }
       public function currentEnrollment()
    {
        // Fetch the latest ACTIVE enrollment
        return $this->hasOne(StudentEnrollment::class)
                    ->where('status', StudentEnrollment::STATUS_ACTIVE)
                    ->latestOfMany();
    }
    public function transportRoute()
    {
        return $this->belongsTo(TransportRoute::class);
    }
    public function getFeeBreakdown($termId)
    {
    $breakdown = collect();

    // 1. Get Class-Based Fees
    $classId = $this->currentEnrollment?->class_id;

    if ($classId) {
        $classFees = \App\Models\ClassFeeStructure::with('feeType')
            ->where('class_id', $classId)
            ->where('term_id', $termId)
            ->get();

        foreach ($classFees as $fee) {
            $breakdown->push([
                'source'      => 'Class',
                'name'        => $fee->feeType->name ?? 'Unnamed Fee',
                'description' => $fee->feeType->description ?? null,
                'amount'      => (float) $fee->amount,
                'is_optional' => (bool) $fee->is_optional,
                'due_date'    => $fee->due_date,
            ]);
        }
    }

    // 2. Get Transport Fee
    if ($this->transportRoute && $this->transportRoute->is_active) {
        $breakdown->push([
            'source'      => 'Transport',
            'name'        => 'Transport: ' . $this->transportRoute->name,
            'description' => 'Daily transport service',
            'amount'      => (float) $this->transportRoute->amount,
            'is_optional' => false, // Transport is typically compulsory if assigned
            'due_date'    => null,
        ]);
    }

    return $breakdown;
}
}