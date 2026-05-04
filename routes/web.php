<?php

use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinanceReportController;

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

    Route::get('/reports/finance', [FinanceReportController::class, 'index'])
        ->name('reports.finance');

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

    Route::get('/reports/finance/export', [FinanceReportController::class, 'export'])
        ->name('reports.finance.export');
});

require __DIR__ . '/auth.php';
