@extends('layouts.admin')

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px;">
        <div>
            <h1 class="text-2xl font-bold">Sumários</h1>
            <p style="color: #6b7280; margin-top: 4px;">
                Gestão dos sumários das aulas e respetivas presenças.
            </p>
        </div>

        <a href="{{ route('lesson-summaries.create') }}"
            style="background: #2563eb; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none;">
            Novo sumário
        </a>
    </div>

    @if(session('success'))
    <div style="background: #ecfdf5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        {{ session('success') }}
    </div>
    @endif

    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('lesson-summaries.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: bold;">Estado</label>
                <select name="status" class="border rounded px-3 py-2">
                    <option value="">Todos</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: bold;">Aula</label>
                <select name="lesson_id" class="border rounded px-3 py-2">
                    <option value="">Todas</option>
                    @foreach($lessons as $lesson)
                    <option value="{{ $lesson->id }}" {{ (string) request('lesson_id') === (string) $lesson->id ? 'selected' : '' }}>
                        {{ $lesson->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: bold;">Professor</label>
                <select name="teacher_id" class="border rounded px-3 py-2">
                    <option value="">Todos</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ (string) request('teacher_id') === (string) $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" style="background: #2563eb; color: white; padding: 10px 14px; border-radius: 6px;">
                Filtrar
            </button>

            <a href="{{ route('lesson-summaries.index') }}"
                style="background: #6b7280; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none;">
                Limpar
            </a>
        </form>
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f9fafb;">
                    <th style="padding: 12px; text-align: left;">Data</th>
                    <th style="padding: 12px; text-align: left;">Aula</th>
                    <th style="padding: 12px; text-align: left;">Professor</th>
                    <th style="padding: 12px; text-align: left;">Estado</th>
                    <th style="padding: 12px; text-align: right;">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse($lessonSummaries as $summary)
                <tr style="border-top: 1px solid #e5e7eb;">
                    <td style="padding: 12px;">
                        {{ $summary->summary_date->format('d/m/Y') }}
                    </td>

                    <td style="padding: 12px;">
                        {{ $summary->lesson->name ?? '—' }}
                    </td>

                    <td style="padding: 12px;">
                        {{ $summary->teacher->name ?? '—' }}
                    </td>

                    <td style="padding: 12px;">
                        @if($summary->status === 'confirmed')
                        <span style="color: #166534; font-weight: bold;">Confirmado</span>
                        @else
                        <span style="color: #92400e; font-weight: bold;">Rascunho</span>
                        @endif
                    </td>

                    <td style="padding: 12px; text-align: right;">
                        <a href="{{ route('lesson-summaries.show', $summary) }}">Ver</a>

                        @if($summary->isDraft())
                        |
                        <a href="{{ route('lesson-summaries.edit', $summary) }}">Editar</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 24px; text-align: center; color: #6b7280;">
                        Ainda não existem sumários registados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection