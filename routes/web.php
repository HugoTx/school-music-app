<?php

use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('students', StudentController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('lessons', LessonController::class);
    Route::resource('enrollments', EnrollmentController::class);
    Route::resource('payments', PaymentController::class);

    Route::get('/reports/finance', function (\Illuminate\Http\Request $request) {
        $selectedYear = $request->query('year');

        $paymentsQuery = \App\Models\Payment::with('enrollment.student', 'enrollment.lesson');

        if ($selectedYear) {
            $paymentsQuery->where('year', $selectedYear);
        }

        $payments = (clone $paymentsQuery)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $totalsQuery = \App\Models\Payment::query();

        if ($selectedYear) {
            $totalsQuery->where('year', $selectedYear);
        }

        $totalPaid = (clone $totalsQuery)->where('paid', true)->sum('amount');
        $totalPending = (clone $totalsQuery)->where('paid', false)->sum('amount');
        $totalAmount = (clone $totalsQuery)->sum('amount');

        $paymentsCount = (clone $totalsQuery)->count();
        $paidCount = (clone $totalsQuery)->where('paid', true)->count();
        $pendingCount = (clone $totalsQuery)->where('paid', false)->count();

        $collectionRate = $totalAmount > 0
            ? round(($totalPaid / $totalAmount) * 100)
            : 0;

        $paymentsByMonthQuery = \App\Models\Payment::selectRaw('year, month, paid, SUM(amount) as total');

        if ($selectedYear) {
            $paymentsByMonthQuery->where('year', $selectedYear);
        }

        $paymentsByMonth = $paymentsByMonthQuery
            ->groupBy('year', 'month', 'paid')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $grouped = $paymentsByMonth
            ->groupBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT))
            ->sortKeys();

        $labels = [];
        $paidData = [];
        $pendingData = [];

        foreach ($grouped as $key => $items) {
            [$year, $month] = explode('-', $key);

            $labels[] = $month . '/' . $year;

            $paidData[] = $items->where('paid', true)->sum('total');
            $pendingData[] = $items->where('paid', false)->sum('total');
        }

        $topDebtorsQuery = \App\Models\Payment::with('enrollment.student')
            ->where('paid', false);

        if ($selectedYear) {
            $topDebtorsQuery->where('year', $selectedYear);
        }

        $topDebtors = $topDebtorsQuery
            ->get()
            ->groupBy(fn($payment) => $payment->enrollment->student->id)
            ->map(function ($payments) {
                $student = $payments->first()->enrollment->student;

                return [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'total_pending' => $payments->sum('amount'),
                    'pending_count' => $payments->count(),
                ];
            })
            ->sortByDesc('total_pending')
            ->take(5)
            ->values();

        $availableYears = \App\Models\Payment::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('reports.finance', compact(
            'payments',
            'totalPaid',
            'totalPending',
            'totalAmount',
            'paymentsCount',
            'paidCount',
            'pendingCount',
            'collectionRate',
            'labels',
            'paidData',
            'pendingData',
            'availableYears',
            'selectedYear',
            'topDebtors'
        ));
    })->name('reports.finance');

    Route::patch('/payments/{payment}/paid', [PaymentController::class, 'markAsPaid'])
        ->name('payments.paid');

    Route::get('/students/{student}/payments', [StudentController::class, 'byStudent'])
        ->name('students.payments');

    Route::get('/students/{student}/payments/create', [PaymentController::class, 'createByStudent'])
        ->name('students.payments.create');

    Route::post('/students/{student}/payments', [PaymentController::class, 'storeByStudent'])
        ->name('students.payments.store');

    Route::get('/students/{student}/payments/{payment}/edit', [PaymentController::class, 'editByStudent'])
        ->name('students.payments.edit');

    Route::put('/students/{student}/payments/{payment}', [PaymentController::class, 'updateByStudent'])
        ->name('students.payments.update');

    Route::delete('/students/{student}/payments/{payment}', [PaymentController::class, 'destroyByStudent'])
        ->name('students.payments.destroy');
});

require __DIR__ . '/auth.php';