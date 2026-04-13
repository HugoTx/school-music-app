<?php

namespace App\Http\Controllers;

use App\Services\FinanceMetricsService;
use Illuminate\Http\Request;

class FinanceReportController extends Controller
{
    public function __construct(
        private FinanceMetricsService $financeMetricsService
    ) {}

    public function index(Request $request)
    {
        $selectedYear = $request->integer('year');

        $data = $this->financeMetricsService->getDashboardData($selectedYear);

        return view('reports.finance', $data);
    }
}
