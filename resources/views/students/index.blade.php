@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Alunos</h1>

<a href="{{ route('students.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
    Novo Aluno
</a>

<ul class="mt-4">
    @foreach($students as $student)
    <li class="p-2 bg-white shadow mb-2 flex justify-between items-center">
        <span>{{ $student->name }}</span>

        <a href="{{ route('students.payments', $student) }}"
            class="text-green-700 underline font-semibold">
            Ver pagamentos →
        </a>


    </li>

    @endforeach
</ul>
@endsection