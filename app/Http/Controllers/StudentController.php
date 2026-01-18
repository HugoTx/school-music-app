<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::latest()->get();
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'birth_date' => 'nullable|date',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'notes' => 'nullable',
        ]);

        Student::create($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Aluno criado com sucesso.');
    }
    public function byStudent($id)
    {
        $student = Student::with([
            'payments.enrollment.lesson'
        ])->findOrFail($id);

        return view('students.payments', compact('student'));
    }
}
