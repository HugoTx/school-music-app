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
        $now = Carbon::now();

        $weekday = strtolower($today->format('l'));

        $lessons = Lesson::with(['teacher', 'enrollments.student'])
            ->where('weekday', $weekday)
            ->orderBy('start_time')
            ->get();

        $summaries = LessonSummary::whereDate('summary_date', $today)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $lessonTimeStates = [];
        $nextLessonId = null;

        foreach ($lessons as $lesson) {
            if (!$lesson->start_time) {
                $lessonTimeStates[$lesson->id] = 'Sem hora';
                continue;
            }

            $start = Carbon::parse($today->format('Y-m-d') . ' ' . $lesson->start_time);
            $end = $lesson->end_time
                ? Carbon::parse($today->format('Y-m-d') . ' ' . $lesson->end_time)
                : $start->copy()->addHour();

            if ($now->between($start, $end)) {
                $lessonTimeStates[$lesson->id] = 'A decorrer';
            } elseif ($now->greaterThan($end)) {
                $lessonTimeStates[$lesson->id] = 'Terminada';
            } else {
                $lessonTimeStates[$lesson->id] = 'Mais tarde';

                if (!$nextLessonId) {
                    $nextLessonId = $lesson->id;
                }
            }
        }

        if ($nextLessonId) {
            $lessonTimeStates[$nextLessonId] = 'A seguir';
        }

        return view('teacher-agenda.index', compact(
            'today',
            'lessons',
            'summaries',
            'nextLessonId',
            'lessonTimeStates'
        ));
    }
}
