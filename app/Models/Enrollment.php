<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Lesson;
use App\Models\Payment;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'lesson_id',
        'start_date',
        'monthly_fee',
        'payment_type',
        'active',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
