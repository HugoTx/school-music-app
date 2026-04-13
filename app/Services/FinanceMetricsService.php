<?php

namespace App\Services;

use App\Models\Payment;

class FinanceMetricsService
{
    public function getDashboardData(?int $selectedYear = null): array
    {
        $paymentsQuery = Payment::with(['enrollment.student', 'enrollment.lesson']);

        if ($selectedYear) {
            $paymentsQuery->where('year', $selectedYear);
        }

        $payments = (clone $paymentsQuery)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $totalsQuery = Payment::query();

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

        $paymentsByMonthQuery = Payment::selectRaw('year, month, paid, SUM(amount) as total');

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

        $topDebtorsQuery = Payment::with('enrollment.student')
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

        $availableYears = Payment::select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return [
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
            'totalAmount' => $totalAmount,
            'paymentsCount' => $paymentsCount,
            'paidCount' => $paidCount,
            'pendingCount' => $pendingCount,
            'collectionRate' => $collectionRate,
            'labels' => $labels,
            'paidData' => $paidData,
            'pendingData' => $pendingData,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'topDebtors' => $topDebtors,
        ];
    }
}
