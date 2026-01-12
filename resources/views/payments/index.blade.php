@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Pagamentos</h1>

<a href="{{ route('payments.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
    Novo Pagamento
</a>

<ul class="mt-4">
    @foreach($payments as $payment)
    <li class="p-2 bg-white shadow mb-2 flex justify-between items-center">
        <div>
            {{ $payment->enrollment->student->name }} —
            {{ $payment->enrollment->lesson->name }} —
            {{ $payment->month }}/{{ $payment->year }} —
            €{{ $payment->amount }} —
            <strong>{{ $payment->paid ? 'Pago' : 'Pendente' }}</strong>
        </div>

        @if(!$payment->paid)
        <form method="POST" action="{{ route('payments.paid', $payment) }}">
            @csrf
            @method('PATCH')
            <button type="submit" style="
    background:#16a34a;
    color:white;
    padding:6px 12px;
    border-radius:6px;
    margin-left:10px;
">
                Marcar como pago
            </button>

        </form>
        @endif
    </li>

    @endforeach
</ul>
@endsection