@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Novo Pagamento</h1>

<form method="POST" action="{{ route('payments.store') }}" class="space-y-4">
    @csrf

    <select name="enrollment_id" class="border p-2 w-full">
        @foreach($enrollments as $enrollment)
        <option value="{{ $enrollment->id }}">
            {{ $enrollment->student->name }} — {{ $enrollment->lesson->name }}
        </option>
        @endforeach
    </select>

    <input name="month" placeholder="Mês" class="border p-2 w-full">
    <input name="year" placeholder="Ano" class="border p-2 w-full">
    <input name="amount" placeholder="Valor" class="border p-2 w-full">

    <button class="bg-green-500 text-white px-4 py-2 rounded">
        Guardar Pagamento
    </button>

</form>
@endsection