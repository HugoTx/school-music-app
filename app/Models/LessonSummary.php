<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonSummary extends Model
{
    protected $fillable = [
        'lesson_id',
        'teacher_id',
        'summary_date',
        'content',
        'status',
        'confirmed_at',
    ];

    protected $casts = [
        'summary_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attendances()
    {
        return $this->hasMany(LessonSummaryAttendance::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}
