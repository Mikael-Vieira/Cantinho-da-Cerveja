<x-app-layout>
    <x-slot name="header">
        <h1>Status do Pedido #{{ $order->id }}</h1>
    </x-slot>

    <div style="max-width: 500px; margin: 2rem auto; padding: 1.5rem; background: var(--bg-card); border-radius: 8px; border: 1px solid var(--border-subtle); text-align: center;">
        <h2>Olá, {{ $order->customer_name }}!</h2>
        <p style="margin: 1rem 0; font-size: 1.2rem;">
            Seu pedido está:
            <strong style="color: var(--accent-green); text-transform: uppercase;">
                {{ str_replace('_', ' ', $order->status) }}
            </strong>
        </p>

        <p style="color: var(--text-muted); font-size: 0.9rem;">Esta página recarrega automaticamente a cada 30 segundos.</p>

        <a href="{{ route('products.index') }}" style="display: inline-block; margin-top: 1.5rem; background: var(--primary); color: #161412; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: bold;">
            Voltar ao Cardápio
        </a>
    </div>

    @push('scripts')
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 30000);
        </script>
    @endpush
</x-app-layout>
