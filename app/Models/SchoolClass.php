<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'grade_level_id',
        'academic_year_id',
        'class_name',
        'stream_id',
    ];

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    // Update the accessor to use the relationship
    public function getFullNameAttribute(): string
    {
        $streamName = $this->stream ? ' ' . $this->stream->name : '';
        return trim(($this->gradeLevel->name ?? '') . $streamName);
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    /**
     * Get the academic year for this class.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the fee structures assigned to this class.
     */
    public function feeStructures()
    {
        return $this->hasMany(ClassFeeStructure::class, 'class_id');
    }

}