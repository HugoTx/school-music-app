<?php

namespace App\Services;

use App\Models\Payment;
use Carbon\Carbon;

class FinanceMetricsService
{
    public function getDashboardData(?int $selectedYear = null): array
    {
        $totals = $this->getTotals($selectedYear);
        $chartData = $this->getChartData($selectedYear);
        $debtAging = $this->getDebtAging($selectedYear);
        $monthlyComparison = $this->getMonthlyComparison();

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
            'debtAging' => $debtAging,
            'monthlyComparison' => $monthlyComparison,
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
        $query = Payment::query()
            ->join('enrollments', 'payments.enrollment_id', '=', 'enrollments.id')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->where('payments.paid', false);

        if ($selectedYear) {
            $query->where('payments.year', $selectedYear);
        }

        return $query
            ->selectRaw('
            students.id as student_id,
            students.name as student_name,
            SUM(payments.amount) as total_pending,
            COUNT(payments.id) as pending_count
        ')
            ->groupBy('students.id', 'students.name')
            ->orderByDesc('total_pending')
            ->limit(5)
            ->get();
    }

    private function getAvailableYears()
    {
        return Payment::select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
    }

    private function getDebtAging(?int $selectedYear = null): array
    {
        $query = Payment::query()
            ->where('paid', false);

        if ($selectedYear) {
            $query->where('year', $selectedYear);
        }

        $payments = $query->get(['month', 'year', 'amount']);

        $today = Carbon::today();

        $aging = [
            'current' => 0,
            '1_30' => 0,
            '31_60' => 0,
            '61_90' => 0,
            '90_plus' => 0,
        ];

        foreach ($payments as $payment) {
            $dueDate = Carbon::createFromDate($payment->year, $payment->month, 1)->endOfMonth();
            $daysOverdue = $dueDate->diffInDays($today, false);

            if ($daysOverdue <= 0) {
                $aging['current'] += $payment->amount;
            } elseif ($daysOverdue <= 30) {
                $aging['1_30'] += $payment->amount;
            } elseif ($daysOverdue <= 60) {
                $aging['31_60'] += $payment->amount;
            } elseif ($daysOverdue <= 90) {
                $aging['61_90'] += $payment->amount;
            } else {
                $aging['90_plus'] += $payment->amount;
            }
        }

        return $aging;
    }
    private function getMonthlyComparison(): array
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $currentMonthPaid = Payment::query()
            ->where('paid', true)
            ->where('month', $currentMonth->month)
            ->where('year', $currentMonth->year)
            ->sum('amount');

        $previousMonthPaid = Payment::query()
            ->where('paid', true)
            ->where('month', $previousMonth->month)
            ->where('year', $previousMonth->year)
            ->sum('amount');

        $currentMonthPending = Payment::query()
            ->where('paid', false)
            ->where('month', $currentMonth->month)
            ->where('year', $currentMonth->year)
            ->sum('amount');

        $previousMonthPending = Payment::query()
            ->where('paid', false)
            ->where('month', $previousMonth->month)
            ->where('year', $previousMonth->year)
            ->sum('amount');

        return [
            'current_month' => [
                'label' => $currentMonth->format('m/Y'),
                'paid' => $currentMonthPaid,
                'pending' => $currentMonthPending,
            ],
            'previous_month' => [
                'label' => $previousMonth->format('m/Y'),
                'paid' => $previousMonthPaid,
                'pending' => $previousMonthPending,
            ],
            'variation' => [
                'paid' => $this->calculatePercentageVariation($currentMonthPaid, $previousMonthPaid),
                'pending' => $this->calculatePercentageVariation($currentMonthPending, $previousMonthPending),
            ],
        ];
    }
    private function calculatePercentageVariation(float|int $current, float|int $previous): ?float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
