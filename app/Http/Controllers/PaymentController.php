<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('enrollment.student', 'enrollment.lesson')->get();

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $enrollments = Enrollment::with('student', 'lesson')->get();

        return view('payments.create', compact('enrollments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0',
        ]);

        $exists = Payment::where('enrollment_id', $request->enrollment_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'duplicate' => 'Já existe um pagamento para esta inscrição neste mês e ano.',
                ]);
        }

        Payment::create($request->only([
            'enrollment_id',
            'month',
            'year',
            'amount',
        ]));

        return redirect()
            ->route('payments.index')
            ->with('success', 'Pagamento criado com sucesso.');
    }

    public function markAsPaid(Payment $payment)
    {
        $payment->update([
            'paid' => true,
            'paid_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Pagamento marcado como pago com sucesso.');
    }

    public function createByStudent(Student $student)
    {
        $enrollments = $student->enrollments()->with('lesson')->get();

        return view('payments.create-by-student', compact('student', 'enrollments'));
    }

    public function storeByStudent(Request $request, Student $student)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0',
        ]);

        $enrollment = $student->enrollments()
            ->where('id', $request->enrollment_id)
            ->firstOrFail();

        $exists = Payment::where('enrollment_id', $enrollment->id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'duplicate' => 'Já existe um pagamento para esta inscrição neste mês e ano.',
                ]);
        }

        Payment::create([
            'enrollment_id' => $enrollment->id,
            'month' => $request->month,
            'year' => $request->year,
            'amount' => $request->amount,
            'paid' => false,
        ]);

        return redirect()
            ->route('students.payments', $student)
            ->with('success', 'Pagamento criado com sucesso.');
    }
}
