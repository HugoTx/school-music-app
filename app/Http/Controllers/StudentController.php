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
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        Student::create($request->only([
            'name',
            'birth_date',
            'email',
            'phone',
            'notes',
        ]));

        return redirect()
            ->route('students.index')
            ->with('success', 'Aluno criado com sucesso.');
    }

    public function byStudent(Student $student)
    {
        $student->load([
            'payments' => function ($query) {
                $query->with('enrollment.lesson')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc');
            }
        ]);

        $totalPaid = $student->payments
            ->filter(fn($p) => $p->paid)
            ->sum('amount');

        $totalPending = $student->payments
            ->filter(fn($p) => !$p->paid)
            ->sum('amount');

        $totalAmount = $student->payments->sum('amount');

        $paymentsCount = $student->payments->count();

        return view('students.payments', compact(
            'student',
            'totalPaid',
            'totalPending',
            'totalAmount',
            'paymentsCount'
        ));
    }
}
