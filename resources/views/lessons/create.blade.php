@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Nova Aula</h1>

<form method="POST" action="{{ route('lessons.store') }}" class="space-y-4">
    @csrf

    <input name="name" placeholder="Nome da aula" class="border p-2 w-full" required>

    <select name="type" class="border p-2 w-full">
        <option value="instrumento">Instrumento</option>
        <option value="teoria">Teoria</option>
        <option value="conjunto">Conjunto</option>
    </select>

    <select name="teacher_id" class="border p-2 w-full">
        @foreach($teachers as $teacher)
        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
        @endforeach
    </select>

    <input name="price" placeholder="Preço mensal" class="border p-2 w-full">

    <textarea name="description" placeholder="Descrição" class="border p-2 w-full"></textarea>

    <button class="bg-green-500 text-white px-4 py-2 rounded">Guardar</button>

</form>
@endsection