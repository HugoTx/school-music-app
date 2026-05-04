<?php

namespace App\Http\Controllers;

use App\Services\FinanceMetricsService;
use Illuminate\Http\Request;
use App\Exports\FinancePaymentsExport;
use Maatwebsite\Excel\Facades\Excel;

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
    public function export(Request $request)
    {
        $selectedYear = $request->integer('year');

        $filename = $selectedYear
            ? "pagamentos-financeiro-{$selectedYear}.xlsx"
            : 'pagamentos-financeiro.xlsx';

        return Excel::download(
            new FinancePaymentsExport($selectedYear),
            $filename
        );
    }
}
