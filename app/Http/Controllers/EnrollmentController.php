<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Lesson;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'lesson'])->get();
        return view('enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        $students = Student::all();
        $lessons = Lesson::all();

        return view('enrollments.create', compact('students', 'lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'lesson_id' => 'required',
            'start_date' => 'required|date',
            'monthly_fee' => 'required|numeric',
        ]);

        Enrollment::create($request->all());

        return redirect()->route('enrollments.index');
    }
}
