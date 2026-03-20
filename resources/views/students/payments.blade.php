@extends('layouts.admin')

@section('content')

<h1 class="text-2xl font-bold mb-4">
    Pagamentos do aluno: {{ $student->name }}
</h1>

@if(session('success'))
<div style="background: #d1fae5; color: #065f46; padding: 10px; border-radius: 6px; margin-bottom: 16px;">
    {{ session('success') }}
</div>
@endif

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
                @if(!$payment->paid)
                <form method="POST" action="{{ route('payments.paid', $payment) }}">
                    @csrf
                    @method('PATCH')

                    <button type="submit" style="background: green; color: white; padding: 6px 12px; border-radius: 6px;">
                        Marcar como pago
                    </button>
                </form>
                @else
                <span style="color: #6b7280;">—</span>
                @endif
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