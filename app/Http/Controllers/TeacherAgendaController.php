<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonSummary;
use Carbon\Carbon;

class TeacherAgendaController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $weekday = strtolower($today->format('l'));

        $lessons = Lesson::with(['teacher', 'enrollments.student'])
            ->where('weekday', $weekday)
            ->orderBy('start_time')
            ->get();

        $summaries = LessonSummary::whereDate('summary_date', $today)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        return view('teacher-agenda.index', compact(
            'today',
            'lessons',
            'summaries'
        ));
    }
}
