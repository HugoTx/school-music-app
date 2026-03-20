@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">
        Novo pagamento - {{ $student->name }}
    </h1>

    <form action="{{ route('students.payments.store', $student->id) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Aula</label>
            <select name="enrollment_id" class="w-full border rounded px-3 py-2">
                @foreach($enrollments as $enrollment)
                <option value="{{ $enrollment->id }}">
                    {{ $enrollment->lesson->name ?? 'Sem aula' }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Mês</label>
            <input type="number" name="month" min="1" max="12" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Ano</label>
            <input type="number" name="year" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Valor</label>
            <input type="number" step="0.01" name="amount" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mt-4">
            <a href="{{ route('students.payments', $student->id) }}"
                class="bg-gray-500 text-white px-4 py-2 rounded inline-block">
                Cancelar
            </a>

            <button type="submit" style="background: green; color: white; padding: 10px;">
                Guardar
            </button>
        </div>
    </form>
</div>
@endsection