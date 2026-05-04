@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">
        Editar pagamento - {{ $student->name }}
    </h1>

    @if($errors->has('duplicate'))
    <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 16px;">
        {{ $errors->first('duplicate') }}
    </div>
    @endif
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 10px; border-radius: 6px; margin-bottom: 16px;">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('students.payments.update', [$student->id, $payment->id]) }}"
        method="POST"
        class="bg-white p-6 rounded shadow">

        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Aula</label>
            <select name="enrollment_id" class="w-full border rounded px-3 py-2">
                @foreach($enrollments as $enrollment)
                <option value="{{ $enrollment->id }}"
                    {{ old('enrollment_id', $payment->enrollment_id) == $enrollment->id ? 'selected' : '' }}>
                    {{ $enrollment->lesson->name ?? 'Sem aula' }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Mês</label>
            <input type="number"
                name="month"
                min="1"
                max="12"
                value="{{ old('month', $payment->month) }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Ano</label>
            <input type="number"
                name="year"
                value="{{ old('year', $payment->year) }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Valor</label>
            <input type="number"
                step="0.01"
                name="amount"
                value="{{ old('amount', $payment->amount) }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="mt-4">
            <a href="{{ route('students.payments', $student->id) }}"
                class="bg-gray-500 text-white px-4 py-2 rounded inline-block">
                Cancelar
            </a>

            <button type="submit"
                style="background: #2563eb; color: white; padding: 10px; border-radius: 6px; margin-left: 8px;">
                Atualizar
            </button>
        </div>
    </form>
</div>
@endsection