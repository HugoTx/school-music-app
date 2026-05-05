<?php

namespace App\Http\Controllers;

use App\Models\LessonSummary;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        LessonSummary::create([
            'lesson_id' => $lesson->id,
            'teacher_id' => $lesson->teacher_id,
            'summary_date' => $validated['summary_date'],
            'content' => $validated['content'] ?? null,
            'status' => $validated['action'] === 'confirm' ? 'confirmed' : 'draft',
            'confirmed_at' => $validated['action'] === 'confirm' ? now() : null,
        ]);

        return redirect()
            ->route('lesson-summaries.index')
            ->with('success', 'Sumário criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
