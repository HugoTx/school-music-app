@extends('layouts.admin')

@section('content')
<div class="container">
    <div style="margin-bottom: 24px;">
        <h1 class="text-2xl font-bold">Novo sumário</h1>
        <p style="color: #6b7280; margin-top: 4px;">
            Registe o sumário de uma aula e guarde como rascunho ou confirme.
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

    <form method="POST" action="{{ route('lesson-summaries.store') }}">
        @csrf

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px;">
                <div>
                    <label for="lesson_id" style="display: block; margin-bottom: 6px; font-weight: bold;">
                        Aula
                    </label>

                    <select name="lesson_id" id="lesson_id" class="border rounded px-3 py-2 w-full" required>
                        <option value="">Selecionar aula</option>
                        @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>
                            {{ $lesson->name }}
                            @if($lesson->teacher)
                            — {{ $lesson->teacher->name }}
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="summary_date" style="display: block; margin-bottom: 6px; font-weight: bold;">
                        Data
                    </label>

                    <input
                        type="date"
                        name="summary_date"
                        id="summary_date"
                        value="{{ old('summary_date', now()->format('Y-m-d')) }}"
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
                    class="border rounded px-3 py-2 w-full"
                    placeholder="Ex: Escalas maiores, leitura rítmica e preparação do repertório...">{{ old('content') }}</textarea>
                <div id="attendances-wrapper" style="margin-top: 24px; display: none;">
                    <h2 class="text-xl font-bold" style="margin-bottom: 12px;">
                        Presenças
                    </h2>

                    <p style="color: #6b7280; margin-bottom: 12px;">
                        Selecione o estado de presença dos alunos inscritos nesta aula.
                    </p>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb;">
                                    <th style="padding: 12px; text-align: left;">Aluno</th>
                                    <th style="padding: 12px; text-align: left;">Estado</th>
                                    <th style="padding: 12px; text-align: left;">Notas</th>
                                </tr>
                            </thead>

                            <tbody id="attendances-table-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <button
                type="submit"
                name="action"
                value="draft"
                style="background: #6b7280; color: white; padding: 10px 14px; border-radius: 6px;">
                Guardar rascunho
            </button>

            <button
                type="submit"
                name="action"
                value="confirm"
                style="background: #2563eb; color: white; padding: 10px 14px; border-radius: 6px;">
                Confirmar sumário
            </button>

            <a href="{{ route('lesson-summaries.index') }}"
                style="color: #6b7280; text-decoration: none;">
                Cancelar
            </a>
        </div>
    </form>
</div>
<script>
    const lessonSelect = document.getElementById('lesson_id');
    const attendancesWrapper = document.getElementById('attendances-wrapper');
    const attendancesTableBody = document.getElementById('attendances-table-body');

    lessonSelect.addEventListener('change', function() {
        const lessonId = this.value;

        attendancesTableBody.innerHTML = '';
        attendancesWrapper.style.display = 'none';

        if (!lessonId) {
            return;
        }

        fetch(`{{ url('/lessons') }}/${lessonId}/students`)
            .then(response => response.json())
            .then(students => {
                if (!students.length) {
                    attendancesTableBody.innerHTML = `
                        <tr>
                            <td colspan="3" style="padding: 18px; text-align: center; color: #6b7280;">
                                Esta aula ainda não tem alunos inscritos.
                            </td>
                        </tr>
                    `;
                    attendancesWrapper.style.display = 'block';
                    return;
                }

                students.forEach(student => {
                    const row = document.createElement('tr');
                    row.style.borderTop = '1px solid #e5e7eb';

                    row.innerHTML = `
                        <td style="padding: 12px;">
                            ${student.name}
                        </td>

                        <td style="padding: 12px;">
                            <select name="attendances[${student.id}][status]" class="border rounded px-3 py-2">
                                <option value="present" selected>Presente</option>
                                <option value="absent">Faltou</option>
                                <option value="justified">Justificada</option>
                            </select>
                        </td>

                        <td style="padding: 12px;">
                            <input
                                type="text"
                                name="attendances[${student.id}][notes]"
                                class="border rounded px-3 py-2 w-full"
                                placeholder="Observações..."
                            >
                        </td>
                    `;

                    attendancesTableBody.appendChild(row);
                });

                attendancesWrapper.style.display = 'block';
            })
            .catch(() => {
                attendancesTableBody.innerHTML = `
                    <tr>
                        <td colspan="3" style="padding: 18px; text-align: center; color: #991b1b;">
                            Não foi possível carregar os alunos desta aula.
                        </td>
                    </tr>
                `;
                attendancesWrapper.style.display = 'block';
            });
    });
</script>
@endsection