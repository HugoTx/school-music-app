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
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div style="background: #ecfdf5; padding: 16px; border-radius: 8px;">
            <p style="color: #065f46; font-size: 14px;">Total recebido</p>
            <h2 style="font-size: 24px; font-weight: bold; color: #065f46;">
                €{{ number_format($totalPaid, 2, ',', '.') }}
            </h2>
        </div>

        <div style="background: #fef2f2; padding: 16px; border-radius: 8px;">
            <p style="color: #991b1b; font-size: 14px;">Total em dívida</p>
            <h2 style="font-size: 24px; font-weight: bold; color: #991b1b;">
                €{{ number_format($totalPending, 2, ',', '.') }}
            </h2>
        </div>

        <div style="background: #eff6ff; padding: 16px; border-radius: 8px;">
            <p style="color: #1d4ed8; font-size: 14px;">Total geral</p>
            <h2 style="font-size: 24px; font-weight: bold; color: #1d4ed8;">
                €{{ number_format($totalAmount, 2, ',', '.') }}
            </h2>
        </div>

        <div style="background: #fefce8; padding: 16px; border-radius: 8px;">
            <p style="color: #854d0e; font-size: 14px;">Taxa de cobrança</p>
            <h2 style="font-size: 24px; font-weight: bold; color: #854d0e;">
                {{ $collectionRate }}%
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div style="background: #f9fafb; padding: 16px; border-radius: 8px;">
            <p style="color: #374151; font-size: 14px;">Nº total de pagamentos</p>
            <h2 style="font-size: 24px; font-weight: bold; color: #111827;">
                {{ $paymentsCount }}
            </h2>
        </div>

        <div style="background: #ecfdf5; padding: 16px; border-radius: 8px;">
            <p style="color: #065f46; font-size: 14px;">Pagamentos pagos</p>
            <h2 style="font-size: 24px; font-weight: bold; color: #065f46;">
                {{ $paidCount }}
            </h2>
        </div>

        <div style="background: #fef2f2; padding: 16px; border-radius: 8px;">
            <p style="color: #991b1b; font-size: 14px;">Pagamentos por pagar</p>
            <h2 style="font-size: 24px; font-weight: bold; color: #991b1b;">
                {{ $pendingCount }}
            </h2>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4 mb-6">
        <h2 class="text-xl font-bold mb-4">Pagamentos por mês</h2>
        <canvas id="paymentsChart" height="100"></canvas>
    </div>

    <div class="bg-white shadow rounded p-4">
        <h2 class="text-xl font-bold mb-4">Lista de pagamentos</h2>

        <div style="overflow-x: auto;">
            <table class="w-full">
                <thead>
                    <tr style="background: #f3f4f6;">
                        <th class="p-2 text-left">Aluno</th>
                        <th class="p-2 text-left">Aula</th>
                        <th class="p-2 text-left">Mês/Ano</th>
                        <th class="p-2 text-left">Valor</th>
                        <th class="p-2 text-left">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr class="border-b">
                        <td class="p-2">
                            {{ $payment->enrollment->student->name ?? '—' }}
                        </td>
                        <td class="p-2">
                            {{ $payment->enrollment->lesson->name ?? '—' }}
                        </td>
                        <td class="p-2">
                            {{ $payment->month }}/{{ $payment->year }}
                        </td>
                        <td class="p-2">
                            €{{ number_format($payment->amount, 2, ',', '.') }}
                        </td>
                        <td class="p-2">
                            @if($payment->paid)
                            <span style="color: #166534; font-weight: bold;">Pago</span>
                            @else
                            <span style="color: #991b1b; font-weight: bold;">Por pagar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">
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
            plugins: {
                legend: {
                    position: 'top'
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