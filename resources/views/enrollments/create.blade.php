@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Nova Matrícula</h1>

<form method="POST" action="{{ route('enrollments.store') }}" class="space-y-4">
    @csrf

    <select name="student_id" class="border p-2 w-full">
        @foreach($students as $student)
        <option value="{{ $student->id }}">{{ $student->name }}</option>
        @endforeach
    </select>

    <select name="lesson_id" class="border p-2 w-full">
        @foreach($lessons as $lesson)
        <option value="{{ $lesson->id }}">{{ $lesson->name }}</option>
        @endforeach
    </select>

    <input type="date" name="start_date" class="border p-2 w-full">

    <input name="monthly_fee" placeholder="Mensalidade" class="border p-2 w-full">

    <button class="bg-green-500 text-white px-4 py-2 rounded">
        Guardar Matrícula
    </button>

</form>
@endsection