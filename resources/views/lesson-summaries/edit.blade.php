@extends('layouts.admin')

@section('content')
<div class="container">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('lesson-summaries.index') }}" style="color: #2563eb; text-decoration: none;">
            ← Voltar aos sumários
        </a>

        <h1 class="text-2xl font-bold" style="margin-top: 12px;">
            Editar rascunho
        </h1>

        <p style="color: #6b7280; margin-top: 4px;">
            Atualize o conteúdo do sumário e as presenças antes de confirmar.
        </p>
    </div>

    @if ($errors->any())
    <div style="background: #fef2f2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <ul style="margin: 0; padding-left: 18px;">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('lesson-summaries.update', $lessonSummary) }}">
        @csrf
        @method('PUT')

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px;">
                <div>
                    <p style="font-size: 13px; color: #6b7280;">Aula</p>
                    <p style="font-weight: bold;">{{ $lessonSummary->lesson->name ?? '—' }}</p>
                </div>

                <div>
                    <p style="font-size: 13px; color: #6b7280;">Professor</p>
                    <p style="font-weight: bold;">{{ $lessonSummary->teacher->name ?? '—' }}</p>
                </div>

                <div>
                    <label for="summary_date" style="display: block; margin-bottom: 6px; font-weight: bold;">
                        Data
                    </label>

                    <input
                        type="date"
                        name="summary_date"
                        id="summary_date"
                        value="{{ old('summary_date', $lessonSummary->summary_date->format('Y-m-d')) }}"
                        class="border rounded px-3 py-2 w-full"
                        required>
                </div>
            </div>

            <div style="margin-top: 16px;">
                <label for="content" style="display: block; margin-bottom: 6px; font-weight: bold;">
                    Sumário
                </label>

                <textarea
                    name="content"
                    id="content"
                    rows="6"
                    class="border rounded px-3 py-2 w-full">{{ old('content', $lessonSummary->content) }}</textarea>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
            <h2 class="text-xl font-bold" style="margin-bottom: 12px;">Presenças</h2>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="padding: 12px; text-align: left;">Aluno</th>
                            <th style="padding: 12px; text-align: left;">Estado</th>
                            <th style="padding: 12px; text-align: left;">Notas</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($lessonSummary->attendances as $attendance)
                        <tr style="border-top: 1px solid #e5e7eb;">
                            <td style="padding: 12px;">
                                {{ $attendance->student->name ?? '—' }}
                            </td>

                            <td style="padding: 12px;">
                                <select
                                    name="attendances[{{ $attendance->id }}][status]"
                                    class="border rounded px-3 py-2">
                                    <option value="present" {{ old("attendances.{$attendance->id}.status", $attendance->status) === 'present' ? 'selected' : '' }}>
                                        Presente
                                    </option>
                                    <option value="absent" {{ old("attendances.{$attendance->id}.status", $attendance->status) === 'absent' ? 'selected' : '' }}>
                                        Faltou
                                    </option>
                                    <option value="justified" {{ old("attendances.{$attendance->id}.status", $attendance->status) === 'justified' ? 'selected' : '' }}>
                                        Justificada
                                    </option>
                                </select>
                            </td>

                            <td style="padding: 12px;">
                                <input
                                    type="text"
                                    name="attendances[{{ $attendance->id }}][notes]"
                                    value="{{ old("attendances.{$attendance->id}.notes", $attendance->notes) }}"
                                    class="border rounded px-3 py-2 w-full"
                                    placeholder="Observações...">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <button
                type="submit"
                name="action"
                value="save"
                style="background: #6b7280; color: white; padding: 10px 14px; border-radius: 6px;">
                Guardar alterações
            </button>

            <button
                type="submit"
                name="action"
                value="confirm"
                style="background: #2563eb; color: white; padding: 10px 14px; border-radius: 6px;">
                Confirmar sumário
            </button>

            <a href="{{ route('lesson-summaries.show', $lessonSummary) }}"
                style="color: #6b7280; text-decoration: none;">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection