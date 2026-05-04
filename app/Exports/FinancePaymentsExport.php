<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinancePaymentsExport implements FromCollection, WithHeadings
{
    protected $year;

    public function __construct($year = null)
    {
        $this->year = $year;
    }

    public function collection()
    {
        $query = Payment::with('enrollment.student', 'enrollment.lesson');

        if ($this->year) {
            $query->where('year', $this->year);
        }

        return $query->get()->map(function ($payment) {
            return [
                'Aluno' => $payment->enrollment->student->name ?? '',
                'Aula' => $payment->enrollment->lesson->name ?? '',
                'Mês' => $payment->month,
                'Ano' => $payment->year,
                'Valor' => $payment->amount,
                'Estado' => $payment->paid ? 'Pago' : 'Por pagar',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Aluno',
            'Aula',
            'Mês',
            'Ano',
            'Valor',
            'Estado',
        ];
    }
}
