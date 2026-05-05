<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonSummaryAttendance extends Model
{
    protected $fillable = [
        'lesson_summary_id',
        'student_id',
        'status',
        'notes',
    ];

    public function lessonSummary()
    {
        return $this->belongsTo(LessonSummary::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
