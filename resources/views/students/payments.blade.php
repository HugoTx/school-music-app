@extends('layouts.admin')

@section('content')

<h1 class="text-2xl font-bold mb-4">
    Pagamentos de {{ $student->name }}
</h1>

<table class="w-full bg-white shadow rounded">
    <tr class="bg-gray-100">
        <th class="p-2">Aula</th>
        <th class="p-2">Mês/Ano</th>
        <th class="p-2">Valor</th>
        <th class="p-2">Estado</th>
    </tr>

    @foreach($student->payments as $payment)
    <tr class="border-t">
        <td class="p-2">
            {{ $payment->enrollment->lesson->name ?? '' }}
        </td>

        <td class="p-2">
            {{ $payment->month }}/{{ $payment->year }}
        </td>

        <td class="p-2">
            € {{ $payment->amount }}
        </td>

        <td class="p-2">
            @if($payment->paid)
            <span class="text-green-600">Pago</span>
            @else
            <span class="text-red-600">Por pagar</span>
            @endif
        </td>
    </tr>
    @endforeach
</table>

@endsection