<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name',
        'level_group',
        'sort_order',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Level Group Constants
     */
    const GROUP_PRE_PRIMARY       = 'PRE_PRIMARY';
    const GROUP_LOWER_PRIMARY     = 'LOWER_PRIMARY';
    const GROUP_UPPER_PRIMARY     = 'UPPER_PRIMARY';
    const GROUP_JUNIOR_SECONDARY  = 'JUNIOR_SECONDARY';
    const GROUP_SENIOR_SECONDARY  = 'SENIOR_SECONDARY';

    /**
     * Get the classes for this grade level.
     */
    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    /**
     * Helper to get human-readable level groups.
     */
    public static function getLevelGroups(): array
    {
        return [
            self::GROUP_PRE_PRIMARY      => 'Pre-Primary',
            self::GROUP_LOWER_PRIMARY    => 'Lower Primary',
            self::GROUP_UPPER_PRIMARY    => 'Upper Primary',
            self::GROUP_JUNIOR_SECONDARY => 'Junior Secondary',
            self::GROUP_SENIOR_SECONDARY => 'Senior Secondary',
        ];
    }

    /**
     * Accessor for a friendly level group name.
     */
    public function getLevelGroupNameAttribute()
    {
        return self::getLevelGroups()[$this->level_group] ?? $this->level_group;
    }
}