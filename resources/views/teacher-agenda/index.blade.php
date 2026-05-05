@extends('layouts.admin')

@section('content')
<div class="container">
    <div style="margin-bottom: 24px;">
        <h1 class="text-2xl font-bold">Agenda de hoje</h1>
        <p style="color: #6b7280; margin-top: 4px;">
            {{ $today->format('d/m/Y') }} — aulas previstas para hoje.
        </p>
    </div>

    @if($lessons->isEmpty())
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; color: #6b7280;">
        Não existem aulas agendadas para hoje.
    </div>
    @else
    <div style="display: grid; gap: 16px;">
        @foreach($lessons as $lesson)
        @php
        $summary = $summaries->get($lesson->id);
        @endphp

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px;">
            <div style="display: flex; justify-content: space-between; gap: 16px; align-items: center; flex-wrap: wrap;">
                <div>
                    <div style="font-size: 14px; color: #6b7280;">
                        {{ $lesson->start_time ? \Carbon\Carbon::parse($lesson->start_time)->format('H:i') : 'Sem hora' }}
                        @if($lesson->end_time)
                        - {{ \Carbon\Carbon::parse($lesson->end_time)->format('H:i') }}
                        @endif
                    </div>

                    <h2 class="text-xl font-bold" style="margin-top: 4px;">
                        {{ $lesson->name }}
                    </h2>

                    <p style="color: #6b7280; margin-top: 4px;">
                        Professor: {{ $lesson->teacher->name ?? '—' }}
                        · Alunos inscritos: {{ $lesson->enrollments->count() }}
                    </p>
                </div>

                <div>
                    @if(!$summary)
                    <a href="{{ route('lesson-summaries.create', ['lesson_id' => $lesson->id, 'summary_date' => $today->format('Y-m-d')]) }}"
                        style="background: #2563eb; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none;">
                        Escrever sumário
                    </a>
                    @elseif($summary->isDraft())
                    <a href="{{ route('lesson-summaries.edit', $summary) }}"
                        style="background: #f59e0b; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none;">
                        Editar rascunho
                    </a>
                    @else
                    <a href="{{ route('lesson-summaries.show', $summary) }}"
                        style="background: #16a34a; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none;">
                        Ver sumário
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection