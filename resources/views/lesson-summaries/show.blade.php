@extends('layouts.admin')

@section('content')
<div class="container">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('lesson-summaries.index') }}" style="color: #2563eb; text-decoration: none;">
            ← Voltar aos sumários
        </a>

        <h1 class="text-2xl font-bold" style="margin-top: 12px;">
            Detalhe do sumário
        </h1>

        <p style="color: #6b7280; margin-top: 4px;">
            Consulte o conteúdo do sumário e as presenças registadas.
        </p>
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;">
            <div>
                <p style="font-size: 13px; color: #6b7280;">Aula</p>
                <p style="font-weight: bold;">{{ $lessonSummary->lesson->name ?? '—' }}</p>
            </div>

            <div>
                <p style="font-size: 13px; color: #6b7280;">Professor</p>
                <p style="font-weight: bold;">{{ $lessonSummary->teacher->name ?? '—' }}</p>
            </div>

            <div>
                <p style="font-size: 13px; color: #6b7280;">Data</p>
                <p style="font-weight: bold;">{{ $lessonSummary->summary_date->format('d/m/Y') }}</p>
            </div>

            <div>
                <p style="font-size: 13px; color: #6b7280;">Estado</p>

                @if($lessonSummary->isConfirmed())
                <span style="color: #166534; font-weight: bold;">Confirmado</span>
                @else
                <span style="color: #92400e; font-weight: bold;">Rascunho</span>
                @endif
            </div>
        </div>
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
        <h2 class="text-xl font-bold" style="margin-bottom: 12px;">Sumário</h2>

        @if($lessonSummary->content)
        <p style="white-space: pre-line; color: #374151;">
            {{ $lessonSummary->content }}
        </p>
        @else
        <p style="color: #6b7280;">
            Ainda não foi escrito conteúdo para este sumário.
        </p>
        @endif
    </div>

    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px;">
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
                    @forelse($lessonSummary->attendances as $attendance)
                    <tr style="border-top: 1px solid #e5e7eb;">
                        <td style="padding: 12px;">
                            {{ $attendance->student->name ?? '—' }}
                        </td>

                        <td style="padding: 12px;">
                            @if($attendance->status === 'present')
                            <span style="color: #166534; font-weight: bold;">Presente</span>
                            @elseif($attendance->status === 'absent')
                            <span style="color: #991b1b; font-weight: bold;">Faltou</span>
                            @else
                            <span style="color: #92400e; font-weight: bold;">Justificada</span>
                            @endif
                        </td>

                        <td style="padding: 12px; color: #6b7280;">
                            {{ $attendance->notes ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding: 24px; text-align: center; color: #6b7280;">
                            Não existem presenças registadas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection