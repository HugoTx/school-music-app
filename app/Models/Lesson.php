<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{

    protected $fillable = [
        'name',
        'type',
        'teacher_id',
        'description',
        'price',
        'weekday',
        'start_time',
        'end_time',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function summaries()
    {
        return $this->hasMany(LessonSummary::class);
    }
}
