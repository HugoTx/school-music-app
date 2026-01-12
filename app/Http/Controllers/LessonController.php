<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('teacher')->get();
        return view('lessons.index', compact('lessons'));
    }

    public function create()
    {
        $teachers = Teacher::all();
        return view('lessons.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'teacher_id' => 'required',
            'price' => 'required|numeric',
        ]);

        Lesson::create($request->all());

        return redirect()->route('lessons.index');
    }
}
