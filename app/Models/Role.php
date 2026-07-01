<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->slug === 'admin';
    }

    public function isMember(): bool
    {
        return $this->slug === 'member';
    }

    public function isGuest(): bool
    {
        return $this->slug === 'guest';
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeAdmin($query)
    {
        return $query->where('slug', 'admin');
    }

    public function scopeMember($query)
    {
        return $query->where('slug', 'member');
    }
}