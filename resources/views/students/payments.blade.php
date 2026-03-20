@extends('layouts.admin')

@section('content')

<h1 class="text-2xl font-bold mb-4">
    Pagamentos do aluno: {{ $student->name }}
</h1>

<a href="{{ route('students.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
    ← Voltar
</a>

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
        @foreach($student->payments as $payment)
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

                    <button type="submit"
                        class="bg-green-600 text-white px-3 py-1 rounded">
                        Marcar como pago
                    </button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection