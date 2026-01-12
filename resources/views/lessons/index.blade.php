@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Aulas</h1>

<a href="{{ route('lessons.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
    Nova Aula
</a>

<ul class="mt-4">
    @foreach($lessons as $lesson)
    <li class="p-2 bg-white shadow mb-2">
        {{ $lesson->name }} — {{ $lesson->teacher->name }} — €{{ $lesson->price }}
    </li>
    @endforeach
</ul>
@endsection