<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cantinho da Cerveja') }}</title>

    {{-- CSS Global --}}
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">

    {{-- Injeção do CSS da página --}}
    @stack('styles')
</head>
<body>

    @include('layouts.navigation')

    @isset($header)
        <header>
            {{ $header }}
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Cantinho da Cerveja - Todos os direitos reservados.</p>
    </footer>

    @stack('scripts')
</body>
</html>
