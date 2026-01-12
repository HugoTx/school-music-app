@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Professores</h1>

<a href="{{ route('teachers.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
    Novo Professor
</a>

<ul class="mt-4">
    @foreach($teachers as $teacher)
    <li class="p-2 bg-white shadow mb-2">
        {{ $teacher->name }}
    </li>
    @endforeach
</ul>
@endsection