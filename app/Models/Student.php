<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}