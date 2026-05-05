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
@endsection