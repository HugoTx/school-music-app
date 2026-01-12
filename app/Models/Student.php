<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'birth_date',
        'email',
        'phone',
        'notes',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
