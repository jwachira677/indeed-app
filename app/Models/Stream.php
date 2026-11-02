<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    protected $fillable = ['name', 'code'];
    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }
}