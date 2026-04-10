@extends('layouts.admin')

@section('content')

<h1 class="text-2xl font-bold mb-4">
    Pagamentos do aluno: {{ $student->name }}
</h1>

<div class="mb-4">
    @if($financialStatus === 'Em dívida')
    <span style="background: #fee2e2; color: #991b1b; padding: 8px 12px; border-radius: 9999px; font-weight: bold;">
        🔴 {{ $financialStatus }}
    </span>
    @else
    <span style="background: #dcfce7; color: #166534; padding: 8px 12px; border-radius: 9999px; font-weight: bold;">
        🟢 {{ $financialStatus }}
    </span>
    @endif
</div>

@if(session('success'))
<div style="background: #d1fae5; color: #065f46; padding: 10px; border-radius: 6px; margin-bottom: 16px;">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div style="background: #ecfdf5; padding: 16px; border-radius: 8px;">
        <p style="color: #065f46; font-size: 14px;">Total pago</p>
        <h2 style="font-size: 24px; font-weight: bold; color: #065f46;">
            €{{ number_format($totalPaid, 2, ',', '.') }}
        </h2>
    </div>

    <div style="background: #fef2f2; padding: 16px; border-radius: 8px;">
        <p style="color: #991b1b; font-size: 14px;">Total por pagar</p>
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

    <div style="background: #f9fafb; padding: 16px; border-radius: 8px;">
        <p style="color: #374151; font-size: 14px;">Nº de pagamentos</p>
        <h2 style="font-size: 24px; font-weight: bold; color: #111827;">
            {{ $paymentsCount }}
        </h2>
    </div>
</div>

<div class="mb-4">
    <a href="{{ route('students.index') }}"
        class="inline-block bg-gray-500 text-white px-4 py-2 rounded">
        ← Voltar
    </a>

    <a href="{{ route('students.payments.create', $student->id) }}"
        style="background: #2563eb; color: white; padding: 8px 12px; border-radius: 6px; margin-left: 8px;">
        + Adicionar pagamento
    </a>
</div>

<table class="w-full mt-4 bg-white shadow rounded">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Aula</th>
            <th class="p-2 text-left">Mês/Ano</th>
            <th class="p-2 text-left">Valor</th>
            <th class="p-2 text-left">Estado</th>
            <th class="p-2 text-left">Pago em</th>
            <th class="p-2 text-left">Ações</th>
        </tr>
    </thead>

    <tbody>
        @forelse($student->payments as $payment)
        <tr class="border-b">
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
                <span class="text-green-700 font-bold">Pago</span>
                @else
                <span class="text-red-700 font-bold">Por pagar</span>
                @endif
            </td>

            <td class="p-2">
                {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '—' }}
            </td>

            <td class="p-2">
                <a href="{{ route('students.payments.edit', [$student->id, $payment->id]) }}"
                    style="background: #2563eb; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; display: inline-block; margin-right: 8px;">
                    Editar
                </a>

                @if(!$payment->paid)
                <form method="POST" action="{{ route('payments.paid', $payment) }}" style="display: inline-block;">
                    @csrf
                    @method('PATCH')

                    <button type="submit" style="background: green; color: white; padding: 6px 12px; border-radius: 6px;">
                        Pago
                    </button>
                </form>
                @endif

                <form method="POST"
                    action="{{ route('students.payments.destroy', [$student->id, $payment->id]) }}"
                    style="display: inline-block;"
                    onsubmit="return confirm('Tens a certeza que queres apagar este pagamento?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        style="background: #dc2626; color: white; padding: 6px 12px; border-radius: 6px; margin-left: 8px;">
                        Apagar
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="p-4 text-center text-gray-500">
                Ainda não existem pagamentos para este aluno.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection