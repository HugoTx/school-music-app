<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Enrollment;
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
            'enrollment_id' => 'required',
            'month' => 'required',
            'year' => 'required',
            'amount' => 'required|numeric',
        ]);

        Payment::create($request->all());

        return redirect()->route('payments.index');
    }

    public function markAsPaid(Payment $payment)
    {
        $payment->update([
            'paid' => true,
            'paid_at' => now(),
        ]);

        return redirect()->route('payments.index');
    }
}
