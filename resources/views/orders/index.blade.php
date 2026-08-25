<x-app-layout>
    <x-slot name="header">
        <h1>Painel de Pedidos (Cozinha / Atendimento)</h1>
    </x-slot>

    <div style="padding: 1rem;">
        @if($orders->isEmpty())
            <p style="text-align: center; color: var(--text-muted); margin-top: 2rem;">Nenhum pedido recebido até o momento.</p>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                @foreach($orders as $order)
                    <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; box-shadow: var(--shadow);">

                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
                            <strong style="font-size: 1.1rem;">Pedido #{{ $order->id }}</strong>
                            <small style="color: var(--text-muted);">{{ $order->created_at->format('H:i') }}</small>
                        </div>

                        <div style="font-size: 0.95rem; margin-bottom: 1rem; line-height: 1.4;">
                            <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
                            <p><strong>Tel:</strong> {{ $order->customer_phone }}</p>
                            <p><strong>Local:</strong> {{ $order->customer_address }}</p>
                            @if($order->customer_complement)
                                <p style="color: var(--text-muted); font-style: italic;"><strong>Obs:</strong> {{ $order->customer_complement }}</p>
                            @endif
                        </div>

                        <div style="border-top: 1px dashed var(--border-subtle); border-bottom: 1px dashed var(--border-subtle); padding: 0.75rem 0; margin-bottom: 1rem;">
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @foreach($order->items as $item)
                                    <li style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                                        <span>{{ $item->quantity }}x {{ $item->product_name }}</span>
                                        <strong>R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                            <div style="text-align: right; font-weight: bold; margin-top: 0.5rem; color: var(--accent-green);">
                                Total: R$ {{ number_format($order->total, 2, ',', '.') }}
                            </div>
                        </div>

                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label style="display: block; font-size: 0.85rem; font-weight: bold; margin-bottom: 0.35rem;">Status do Pedido:</label>
                            <select name="status" onchange="this.form.submit()" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-main); color: var(--text-main); font-weight: bold;">
                                <option value="pendente" {{ $order->status === 'pendente' ? 'selected' : '' }}>🟡 Pendente</option>
                                <option value="em_preparo" {{ $order->status === 'em_preparo' ? 'selected' : '' }}>🔵 Em Preparo</option>
                                <option value="pronto" {{ $order->status === 'pronto' ? 'selected' : '' }}>🟢 Pronto</option>
                                <option value="entregue" {{ $order->status === 'entregue' ? 'selected' : '' }}>⚪ Entregue</option>
                            </select>
                        </form>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Recarrega a tela a cada 30 segundos
            setTimeout(function() {
                window.location.reload();
            }, 30000);
        </script>
    @endpush
</x-app-layout>
