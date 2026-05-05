<?php

namespace App\Http\Controllers;

use App\Models\LessonSummary;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\LessonSummaryAttendance;

class LessonSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LessonSummary::with(['lesson', 'teacher'])
            ->orderByDesc('summary_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $lessonSummaries = $query->get();

        $lessons = Lesson::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();

        return view('lesson-summaries.index', compact(
            'lessonSummaries',
            'lessons',
            'teachers'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lessons = Lesson::with('teacher')
            ->orderBy('name')
            ->get();

        return view('lesson-summaries.create', compact('lessons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'summary_date' => 'required|date',
            'content' => 'nullable|string',
            'action' => 'required|in:draft,confirm',
            'attendances' => 'nullable|array',
            'attendances.*.status' => 'required|in:present,absent,justified',
            'attendances.*.notes' => 'nullable|string',
        ]);

        $lesson = Lesson::with('teacher')->findOrFail($validated['lesson_id']);

        $exists = LessonSummary::where('lesson_id', $lesson->id)
            ->whereDate('summary_date', $validated['summary_date'])
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'duplicate' => 'Já existe um sumário para esta aula nesta data.',
                ]);
        }

        $summary = LessonSummary::create([
            'lesson_id' => $lesson->id,
            'teacher_id' => $lesson->teacher_id,
            'summary_date' => $validated['summary_date'],
            'content' => $validated['content'] ?? null,
            'status' => $validated['action'] === 'confirm' ? 'confirmed' : 'draft',
            'confirmed_at' => $validated['action'] === 'confirm' ? now() : null,
        ]);

        $lesson->load('enrollments.student');

        foreach ($lesson->enrollments as $enrollment) {
            $studentId = $enrollment->student_id;
            $attendanceData = $validated['attendances'][$studentId] ?? null;

            LessonSummaryAttendance::create([
                'lesson_summary_id' => $summary->id,
                'student_id' => $studentId,
                'status' => $attendanceData['status'] ?? 'present',
                'notes' => $attendanceData['notes'] ?? null,
            ]);
        }
        return redirect()
            ->route('lesson-summaries.index')
            ->with('success', 'Sumário criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LessonSummary $lessonSummary)
    {
        $lessonSummary->load([
            'lesson',
            'teacher',
            'attendances.student',
        ]);

        return view('lesson-summaries.show', compact('lessonSummary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LessonSummary $lessonSummary)
    {
        if ($lessonSummary->isConfirmed()) {
            return redirect()
                ->route('lesson-summaries.show', $lessonSummary)
                ->with('error', 'Não é possível editar um sumário confirmado.');
        }

        $lessonSummary->load([
            'lesson',
            'teacher',
            'attendances.student',
        ]);

        return view('lesson-summaries.edit', compact('lessonSummary'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LessonSummary $lessonSummary)
    {
        if ($lessonSummary->isConfirmed()) {
            return redirect()
                ->route('lesson-summaries.show', $lessonSummary)
                ->with('error', 'Sumários confirmados não podem ser editados.');
        }

        $validated = $request->validate([
            'summary_date' => 'required|date',
            'content' => 'nullable|string',
            'action' => 'required|in:save,confirm',
            'attendances' => 'nullable|array',
            'attendances.*.status' => 'required|in:present,absent,justified',
            'attendances.*.notes' => 'nullable|string',
        ]);

        $exists = LessonSummary::where('lesson_id', $lessonSummary->lesson_id)
            ->whereDate('summary_date', $validated['summary_date'])
            ->where('id', '!=', $lessonSummary->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'duplicate' => 'Já existe um sumário para esta aula nesta data.',
                ]);
        }

        $lessonSummary->update([
            'summary_date' => $validated['summary_date'],
            'content' => $validated['content'] ?? null,
            'status' => $validated['action'] === 'confirm' ? 'confirmed' : 'draft',
            'confirmed_at' => $validated['action'] === 'confirm' ? now() : null,
        ]);

        foreach ($validated['attendances'] ?? [] as $attendanceId => $attendanceData) {
            $lessonSummary->attendances()
                ->where('id', $attendanceId)
                ->update([
                    'status' => $attendanceData['status'],
                    'notes' => $attendanceData['notes'] ?? null,
                ]);
        }

        return redirect()
            ->route('lesson-summaries.show', $lessonSummary)
            ->with('success', 'Sumário atualizado com sucesso.');
    }

    public function studentsByLesson(Lesson $lesson)
    {
        $lesson->load('enrollments.student');

        return response()->json(
            $lesson->enrollments
                ->map(fn($enrollment) => [
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->name,
                ])
                ->values()
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
