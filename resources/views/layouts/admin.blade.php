<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>School Música App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-white shadow">
            <h2 class="p-4 font-bold text-xl">🎵 School Música</h2>

            <nav class="p-4 space-y-2">
                <a href="/dashboard" class="block">Dashboard</a>
                <a href="/students" class="block">Alunos</a>
                <a href="#" class="block">Professores</a>
                <a href="#" class="block">Aulas</a>
                <a href="#" class="block">Pagamentos</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</body>

</html>