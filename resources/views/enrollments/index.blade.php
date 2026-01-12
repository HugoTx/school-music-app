@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Matrículas</h1>

<a href="{{ route('enrollments.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
    Nova Matrícula
</a>

<ul class="mt-4">
    @foreach($enrollments as $enrollment)
    <li class="p-2 bg-white shadow mb-2">
        {{ $enrollment->student->name }} — {{ $enrollment->lesson->name }} — €{{ $enrollment->monthly_fee }}
    </li>
    @endforeach
</ul>
@endsection