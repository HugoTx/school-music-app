<?php

namespace App\Services;

use App\Models\Payment;

class FinanceMetricsService
{
    public function getDashboardData(?int $selectedYear = null): array
    {
        $totals = $this->getTotals($selectedYear);
        $chartData = $this->getChartData($selectedYear);

        return [
            'payments' => $this->getPayments($selectedYear),
            'totalPaid' => $totals['totalPaid'],
            'totalPending' => $totals['totalPending'],
            'totalAmount' => $totals['totalAmount'],
            'paymentsCount' => $totals['paymentsCount'],
            'paidCount' => $totals['paidCount'],
            'pendingCount' => $totals['pendingCount'],
            'collectionRate' => $totals['collectionRate'],
            'labels' => $chartData['labels'],
            'paidData' => $chartData['paidData'],
            'pendingData' => $chartData['pendingData'],
            'availableYears' => $this->getAvailableYears(),
            'selectedYear' => $selectedYear,
            'topDebtors' => $this->getTopDebtors($selectedYear),
        ];
    }

    private function getPayments(?int $selectedYear = null)
    {
        $query = Payment::with(['enrollment.student', 'enrollment.lesson']);

        if ($selectedYear) {
            $query->where('year', $selectedYear);
        }

        return $query
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();
    }

    private function getTotals(?int $selectedYear = null): array
    {
        $query = Payment::query();

        if ($selectedYear) {
            $query->where('year', $selectedYear);
        }

        $totalPaid = (clone $query)->where('paid', true)->sum('amount');
        $totalPending = (clone $query)->where('paid', false)->sum('amount');
        $totalAmount = (clone $query)->sum('amount');

        $paymentsCount = (clone $query)->count();
        $paidCount = (clone $query)->where('paid', true)->count();
        $pendingCount = (clone $query)->where('paid', false)->count();

        return [
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
            'totalAmount' => $totalAmount,
            'paymentsCount' => $paymentsCount,
            'paidCount' => $paidCount,
            'pendingCount' => $pendingCount,
            'collectionRate' => $totalAmount > 0
                ? round(($totalPaid / $totalAmount) * 100)
                : 0,
        ];
    }

    private function getChartData(?int $selectedYear = null): array
    {
        $query = Payment::selectRaw('year, month, paid, SUM(amount) as total');

        if ($selectedYear) {
            $query->where('year', $selectedYear);
        }

        $paymentsByMonth = $query
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

        return [
            'labels' => $labels,
            'paidData' => $paidData,
            'pendingData' => $pendingData,
        ];
    }

    private function getTopDebtors(?int $selectedYear = null)
    {
        $query = Payment::with('enrollment.student')
            ->where('paid', false);

        if ($selectedYear) {
            $query->where('year', $selectedYear);
        }

        return $query
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
    }

    private function getAvailableYears()
    {
        return Payment::select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
    }
}
