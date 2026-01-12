@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Relatório Financeiro</h1>

<p class="mb-4">Total recebido: <strong>€{{ $total }}</strong></p>

<ul>
    @foreach($payments as $payment)
    <li class="p-2 bg-white shadow mb-2">
        {{ $payment->enrollment->student->name }} —
        {{ $payment->enrollment->lesson->name }} —
        €{{ $payment->amount }}
    </li>
    @endforeach
</ul>
@endsection