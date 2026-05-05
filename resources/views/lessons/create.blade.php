@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-6">Nova Aula</h1>

    @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('lessons.store') }}" class="space-y-6">
        @csrf

        {{-- Nome --}}
        <div>
            <label class="block font-semibold mb-1">Nome da aula</label>
            <input
                name="name"
                value="{{ old('name') }}"
                placeholder="Ex: Piano Iniciação"
                class="border p-2 w-full rounded"
                required>
        </div>

        {{-- Tipo --}}
        <div>
            <label class="block font-semibold mb-1">Tipo</label>
            <select name="type" class="border p-2 w-full rounded">
                <option value="instrumento" {{ old('type') === 'instrumento' ? 'selected' : '' }}>Instrumento</option>
                <option value="teoria" {{ old('type') === 'teoria' ? 'selected' : '' }}>Teoria</option>
                <option value="conjunto" {{ old('type') === 'conjunto' ? 'selected' : '' }}>Conjunto</option>
            </select>
        </div>

        {{-- Professor --}}
        <div>
            <label class="block font-semibold mb-1">Professor</label>
            <select name="teacher_id" class="border p-2 w-full rounded">
                <option value="">Selecionar professor</option>
                @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                    {{ $teacher->name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Horário --}}
        <div>
            <label class="block font-semibold mb-2">Horário</label>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Dia --}}
                <div>
                    <label class="block text-sm mb-1">Dia da semana</label>
                    <select name="weekday" class="border rounded px-3 py-2 w-full">
                        <option value="">Selecionar</option>
                        <option value="monday" {{ old('weekday') === 'monday' ? 'selected' : '' }}>Segunda</option>
                        <option value="tuesday" {{ old('weekday') === 'tuesday' ? 'selected' : '' }}>Terça</option>
                        <option value="wednesday" {{ old('weekday') === 'wednesday' ? 'selected' : '' }}>Quarta</option>
                        <option value="thursday" {{ old('weekday') === 'thursday' ? 'selected' : '' }}>Quinta</option>
                        <option value="friday" {{ old('weekday') === 'friday' ? 'selected' : '' }}>Sexta</option>
                        <option value="saturday" {{ old('weekday') === 'saturday' ? 'selected' : '' }}>Sábado</option>
                        <option value="sunday" {{ old('weekday') === 'sunday' ? 'selected' : '' }}>Domingo</option>
                    </select>
                </div>

                {{-- Hora início --}}
                <div>
                    <label class="block text-sm mb-1">Hora início</label>
                    <input
                        type="time"
                        name="start_time"
                        value="{{ old('start_time') }}"
                        class="border rounded px-3 py-2 w-full">
                </div>

                {{-- Hora fim --}}
                <div>
                    <label class="block text-sm mb-1">Hora fim</label>
                    <input
                        type="time"
                        name="end_time"
                        value="{{ old('end_time') }}"
                        class="border rounded px-3 py-2 w-full">
                </div>
            </div>
        </div>

        {{-- Preço --}}
        <div>
            <label class="block font-semibold mb-1">Preço mensal (€)</label>
            <input
                name="price"
                value="{{ old('price') }}"
                placeholder="Ex: 30"
                class="border p-2 w-full rounded">
        </div>

        {{-- Descrição --}}
        <div>
            <label class="block font-semibold mb-1">Descrição</label>
            <textarea
                name="description"
                placeholder="Descrição da aula..."
                class="border p-2 w-full rounded"
                rows="4">{{ old('description') }}</textarea>
        </div>

        {{-- Botão --}}
        <div class="flex gap-3">
            <button class="bg-green-500 text-white px-4 py-2 rounded">
                Guardar
            </button>

            <a href="{{ route('lessons.index') }}" class="text-gray-600">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection