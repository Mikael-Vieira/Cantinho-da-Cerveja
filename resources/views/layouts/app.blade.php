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

    @php
        $usaSidebar = Auth::check() && in_array(Auth::user()->role, ['admin', 'funcionario']);
    @endphp

    @if ($usaSidebar)
        <div class="app-shell-with-sidebar">
            @include('layouts.sidebar')

            <div class="app-content">
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
            </div>
        </div>
    @else
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
    @endif

    {{-- Modal Pop-up de Sucesso do Pedido --}}
    @if (session('order_success'))
        <div id="orderModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.65); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;">
            <div style="background: var(--bg-card); border: 1px solid var(--accent-green); border-radius: var(--radius); padding: 2rem; max-width: 420px; width: 100%; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.4);">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎉</div>
                <h2 style="color: var(--primary); font-size: 1.5rem; margin-bottom: 0.5rem;">Pedido Realizado!</h2>
                <p style="color: var(--text-main); font-size: 1rem; margin-bottom: 1.5rem; line-height: 1.4;">
                    {{ session('order_success') }}
                </p>
                <button onclick="document.getElementById('orderModal').remove()" style="background-color: var(--primary); color: #1E1B18; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: bold; font-size: 1rem; cursor: pointer; width: 100%;">
                    Entendido / Fechar
                </button>
            </div>
        </div>
    @endif

    @stack('scripts')
</body>
</html>
