@extends('layouts.admin')

@section('content')

<div class="container">
    <h1 class="text-2xl font-bold mb-4">
        Dashboard Financeiro
        @if($selectedYear)
        - {{ $selectedYear }}
        @endif
    </h1>

    <div class="mb-6">
        <form method="GET" action="{{ route('reports.finance') }}" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
            <div>
                <label for="year" style="display: block; margin-bottom: 6px; font-weight: bold;">Filtrar por ano</label>
                <select name="year" id="year" class="border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ (string) $selectedYear === (string) $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" style="background: #2563eb; color: white; padding: 10px 14px; border-radius: 6px;">
                    Aplicar filtro
                </button>
            </div>

            @if($selectedYear)
            <div>
                <a href="{{ route('reports.finance') }}" style="background: #6b7280; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none; display: inline-block;">
                    Limpar filtro
                </a>
            </div>
            @endif
            <div>
                <a href="{{ route('reports.finance.export', ['year' => $selectedYear]) }}"
                    style="background: #16a34a; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none; display: inline-block;">
                    Exportar Excel
                </a>
            </div>
        </form>
    </div>

    {{-- KPIs principais --}}
    <div style="background: #ffffff; border: 1px solid #d1d5db; border-left: 5px solid #16a34a; border-radius: 8px; padding: 18px 22px; margin-bottom: 24px;">
        <div style="text-align: center; color: #6b7280; font-size: 13px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 18px;">
            Resumo financeiro
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; text-align: center;">
            <div style="padding: 0 16px;">
                <div style="font-size: 28px; font-weight: 800; color: #047857;">
                    €{{ number_format($totalPaid, 2, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    Recebido
                </div>
            </div>

            <div style="padding: 0 16px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 28px; font-weight: 800; color: #b91c1c;">
                    €{{ number_format($totalPending, 2, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    Em dívida
                </div>
            </div>

            <div style="padding: 0 16px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 28px; font-weight: 800; color: #2563eb;">
                    €{{ number_format($totalAmount, 2, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    Total
                </div>
            </div>

            <div style="padding: 0 16px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 28px; font-weight: 800; color: #92400e;">
                    {{ $collectionRate }}%
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    Cobrança
                </div>
            </div>
        </div>
    </div>
    {{-- Comparação mensal --}}
    <div style="background: #ffffff; border: 1px solid #d1d5db; border-left: 5px solid #0ea5e9; border-radius: 8px; padding: 18px 22px; margin-bottom: 24px;">
        <div style="text-align: center; color: #6b7280; font-size: 13px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 18px;">
            Comparação mensal
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); text-align: center;">
            <div style="padding: 0 16px;">
                <div style="font-size: 13px; color: #6b7280; font-weight: 700; text-transform: uppercase;">
                    Recebido este mês
                </div>

                <div style="font-size: 26px; font-weight: 800; color: #047857; margin-top: 6px;">
                    €{{ number_format($monthlyComparison['current_month']['paid'], 2, ',', '.') }}
                </div>

                <div style="font-size: 12px; color: #6b7280; margin-top: 6px;">
                    {{ $monthlyComparison['current_month']['label'] }} vs {{ $monthlyComparison['previous_month']['label'] }}
                </div>

                <div style="font-size: 13px; font-weight: 700; margin-top: 8px;">
                    @if(is_null($monthlyComparison['variation']['paid']))
                    <span style="color: #6b7280;">Sem dados comparáveis</span>
                    @elseif($monthlyComparison['variation']['paid'] >= 0)
                    <span style="color: #047857;">+{{ $monthlyComparison['variation']['paid'] }}% vs mês anterior</span>
                    @else
                    <span style="color: #b91c1c;">{{ $monthlyComparison['variation']['paid'] }}% vs mês anterior</span>
                    @endif
                </div>
            </div>

            <div style="padding: 0 16px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 13px; color: #6b7280; font-weight: 700; text-transform: uppercase;">
                    Em dívida este mês
                </div>

                <div style="font-size: 26px; font-weight: 800; color: #b91c1c; margin-top: 6px;">
                    €{{ number_format($monthlyComparison['current_month']['pending'], 2, ',', '.') }}
                </div>

                <div style="font-size: 12px; color: #6b7280; margin-top: 6px;">
                    {{ $monthlyComparison['current_month']['label'] }} vs {{ $monthlyComparison['previous_month']['label'] }}
                </div>

                <div style="font-size: 13px; font-weight: 700; margin-top: 8px;">
                    @if(is_null($monthlyComparison['variation']['pending']))
                    <span style="color: #6b7280;">Sem dados comparáveis</span>
                    @elseif($monthlyComparison['variation']['pending'] > 0)
                    <span style="color: #b91c1c;">+{{ $monthlyComparison['variation']['pending'] }}% vs mês anterior</span>
                    @elseif($monthlyComparison['variation']['pending'] < 0)
                        <span style="color: #047857;">{{ $monthlyComparison['variation']['pending'] }}% vs mês anterior</span>
                        @else
                        <span style="color: #6b7280;">0% vs mês anterior</span>
                        @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Contadores --}}
    <div style="background: #ffffff; border: 1px solid #d1d5db; border-left: 5px solid #3b82f6; border-radius: 8px; padding: 18px 22px; margin-bottom: 24px;">
        <div style="text-align: center; color: #6b7280; font-size: 13px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 18px;">
            Pagamentos
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); text-align: center;">
            <div style="padding: 0 16px;">
                <div style="font-size: 26px; font-weight: 800; color: #111827;">
                    {{ $paymentsCount }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    Total
                </div>
            </div>

            <div style="padding: 0 16px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 26px; font-weight: 800; color: #047857;">
                    {{ $paidCount }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    Pagos
                </div>
            </div>

            <div style="padding: 0 16px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 26px; font-weight: 800; color: #b91c1c;">
                    {{ $pendingCount }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    Por pagar
                </div>
            </div>
        </div>
    </div>

    {{-- Aging da dívida --}}
    <div style="background: #ffffff; border: 1px solid #d1d5db; border-left: 5px solid #ef4444; border-radius: 8px; padding: 18px 22px; margin-bottom: 24px;">
        <div style="text-align: center; color: #6b7280; font-size: 13px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 8px;">
            Antiguidade da dívida
        </div>

        <p style="text-align: center; color: #6b7280; font-size: 13px; margin-bottom: 18px;">
            Distribuição dos valores em dívida por tempo de atraso.
        </p>

        <div style="display: grid; grid-template-columns: repeat(5, 1fr); text-align: center;">
            <div style="padding: 0 12px;">
                <div style="font-size: 22px; font-weight: 800; color: #111827;">
                    €{{ number_format($debtAging['current'], 2, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    Não vencido
                </div>
            </div>

            <div style="padding: 0 12px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 22px; font-weight: 800; color: #ca8a04;">
                    €{{ number_format($debtAging['1_30'], 2, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    1–30 dias
                </div>
            </div>

            <div style="padding: 0 12px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 22px; font-weight: 800; color: #ea580c;">
                    €{{ number_format($debtAging['31_60'], 2, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    31–60 dias
                </div>
            </div>

            <div style="padding: 0 12px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 22px; font-weight: 800; color: #dc2626;">
                    €{{ number_format($debtAging['61_90'], 2, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    61–90 dias
                </div>
            </div>

            <div style="padding: 0 12px; border-left: 2px solid #e5e7eb;">
                <div style="font-size: 22px; font-weight: 800; color: #991b1b;">
                    €{{ number_format($debtAging['90_plus'], 2, ',', '.') }}
                </div>
                <div style="font-size: 12px; color: #6b7280; text-transform: uppercase; margin-top: 4px;">
                    +90 dias
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico --}}
    <div style="background: #ffffff; border: 1px solid #d1d5db; border-left: 5px solid #6366f1; border-radius: 8px; padding: 18px 22px; margin-bottom: 24px;">
        <div style="text-align: center; color: #6b7280; font-size: 13px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 6px;">
            Pagamentos por mês
        </div>

        <p style="text-align: center; color: #6b7280; font-size: 13px; margin-bottom: 18px;">
            Valores recebidos vs valores em dívida ao longo dos meses.
        </p>

        <div style="height: 280px; position: relative;">
            <canvas id="paymentsChart"></canvas>
        </div>
    </div>

    {{-- Tabela --}}
    <div style="background: #ffffff; border: 1px solid #d1d5db; border-left: 5px solid #64748b; border-radius: 8px; padding: 18px 22px; margin-bottom: 24px;">
        <div style="text-align: center; color: #6b7280; font-size: 13px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 6px;">
            Lista de pagamentos
        </div>

        <p style="text-align: center; color: #6b7280; font-size: 13px; margin-bottom: 18px;">
            Detalhe dos pagamentos registados no sistema.
        </p>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <th style="padding: 12px 10px; text-align: left; color: #6b7280; font-size: 12px; text-transform: uppercase;">Aluno</th>
                        <th style="padding: 12px 10px; text-align: left; color: #6b7280; font-size: 12px; text-transform: uppercase;">Aula</th>
                        <th style="padding: 12px 10px; text-align: left; color: #6b7280; font-size: 12px; text-transform: uppercase;">Mês/Ano</th>
                        <th style="padding: 12px 10px; text-align: right; color: #6b7280; font-size: 12px; text-transform: uppercase;">Valor</th>
                        <th style="padding: 12px 10px; text-align: center; color: #6b7280; font-size: 12px; text-transform: uppercase;">Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 12px 10px; font-weight: 600; color: #111827;">
                            {{ $payment->enrollment->student->name ?? '—' }}
                        </td>

                        <td style="padding: 12px 10px; color: #4b5563;">
                            {{ $payment->enrollment->lesson->name ?? '—' }}
                        </td>

                        <td style="padding: 12px 10px; color: #4b5563;">
                            {{ $payment->month }}/{{ $payment->year }}
                        </td>

                        <td style="padding: 12px 10px; text-align: right; font-weight: 700; color: #111827;">
                            €{{ number_format($payment->amount, 2, ',', '.') }}
                        </td>

                        <td style="padding: 12px 10px; text-align: center;">
                            @if($payment->paid)
                            <span style="display: inline-block; background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">
                                Pago
                            </span>
                            @else
                            <span style="display: inline-block; background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700;">
                                Por pagar
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 28px 10px; text-align: center; color: #6b7280;">
                            Ainda não existem pagamentos registados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('paymentsChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{
                    label: 'Recebido',
                    data: @json($paidData),
                    backgroundColor: '#16a34a'
                },
                {
                    label: 'Em dívida',
                    data: @json($pendingData),
                    backgroundColor: '#dc2626'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection