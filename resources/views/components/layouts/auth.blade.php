@props([
    'title' => 'Masuk | PhysioAdmin',
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top,_#dbeafe,_#eff6ff_35%,_#f8fafc_70%)] font-sans text-slate-800">
    <main class="mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10 sm:px-6">
        {{ $slot }}
    </main>
</body>
</html>
