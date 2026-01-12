@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Novo Professor</h1>

<form method="POST" action="{{ route('teachers.store') }}" class="space-y-4">
    @csrf

    <input name="name" placeholder="Nome" class="border p-2 w-full" required>
    <input name="email" placeholder="Email" class="border p-2 w-full">
    <input name="phone" placeholder="Telefone" class="border p-2 w-full">
    <input name="instrument" placeholder="Instrumento" class="border p-2 w-full">
    <textarea name="notes" placeholder="Notas" class="border p-2 w-full"></textarea>

    <button class="bg-green-500 text-white px-4 py-2 rounded">
        Guardar
    </button>
</form>
@endsection