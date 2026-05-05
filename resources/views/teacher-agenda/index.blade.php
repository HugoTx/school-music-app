@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Agenda de hoje</h1>
            <p class="text-gray-500">
                {{ $today->format('d/m/Y') }} — aulas previstas para hoje.
            </p>
        </div>

        <div class="bg-white border rounded-lg px-3 py-1 text-sm shadow-sm text-gray-700">
            {{ $lessons->count() }} {{ $lessons->count() === 1 ? 'aula' : 'aulas' }}
        </div>
    </div>

    @if($lessons->isEmpty())
    <div class="bg-white border rounded-xl p-8 text-center text-gray-500 shadow-sm">
        <div class="text-4xl mb-3">🎵</div>
        <h2 class="text-lg font-semibold text-gray-800">Sem aulas para hoje</h2>
        <p class="mt-1">Não existem aulas agendadas para hoje.</p>
    </div>
    @else

    <div class="space-y-4">
        @foreach($lessons as $lesson)
        @php
        $summary = $summaries->get($lesson->id);
        $timeState = $lessonTimeStates[$lesson->id] ?? null;

        if (!$summary) {
        $status = 'Sem sumário';
        $badgeClass = 'bg-gray-100 text-gray-700';
        $borderClass = 'border-gray-500';
        $btnText = 'Escrever sumário';
        $btnClass = 'bg-blue-600 hover:bg-blue-700';
        $route = route('lesson-summaries.create', [
        'lesson_id' => $lesson->id,
        'summary_date' => $today->format('Y-m-d')
        ]);
        } elseif ($summary->isDraft()) {
        $status = 'Rascunho';
        $badgeClass = 'bg-yellow-100 text-yellow-700';
        $borderClass = 'border-yellow-500';
        $btnText = 'Editar rascunho';
        $btnClass = 'bg-yellow-500 hover:bg-yellow-600';
        $route = route('lesson-summaries.edit', $summary);
        } else {
        $status = 'Confirmado';
        $badgeClass = 'bg-green-100 text-green-700';
        $borderClass = 'border-green-500';
        $btnText = 'Ver sumário';
        $btnClass = 'bg-green-600 hover:bg-green-700';
        $route = route('lesson-summaries.show', $summary);
        }
        @endphp

        <div
            @if(in_array($timeState, ['A decorrer', 'A seguir' ]))
            id="active-lesson"
            @endif
            class="bg-white border-l-4 {{ $borderClass }} rounded-xl p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="flex items-center justify-between gap-6">

                <!-- Info -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-sm flex-wrap">

                        @if($timeState)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            {{ $timeState }}
                        </span>
                        @endif

                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                            {{ $status }}
                        </span>

                        <span class="text-gray-500">
                            {{ $lesson->start_time ? \Carbon\Carbon::parse($lesson->start_time)->format('H:i') : 'Sem hora' }}
                            @if($lesson->end_time)
                            - {{ \Carbon\Carbon::parse($lesson->end_time)->format('H:i') }}
                            @endif
                        </span>
                    </div>

                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ $lesson->name }}
                    </h2>

                    <div class="text-sm text-gray-500 flex gap-4 flex-wrap">
                        <span>👨‍🏫 {{ $lesson->teacher->name ?? 'Sem professor' }}</span>
                        <span>👥 {{ $lesson->enrollments->count() }} {{ $lesson->enrollments->count() === 1 ? 'aluno' : 'alunos' }}</span>
                    </div>

                    @if(!$summary)
                    <p class="text-xs text-gray-400">
                        Ainda não existe sumário para esta aula.
                    </p>
                    @endif
                </div>

                <!-- Action -->
                <div class="shrink-0">
                    <a href="{{ $route }}"
                        class="inline-flex items-center justify-center text-white px-4 py-2 rounded-lg {{ $btnClass }} text-sm font-medium shadow-sm hover:shadow transition whitespace-nowrap">
                        {{ $btnText }}
                    </a>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    @endif

</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activeLesson = document.getElementById('active-lesson');

        if (activeLesson) {
            activeLesson.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    });
</script>
@endsection