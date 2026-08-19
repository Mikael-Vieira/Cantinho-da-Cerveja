<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    @endpush

    <x-slot name="header">
        <h1>Seu Pedido</h1>
    </x-slot>

    @if(session('success'))
        <div style="background-color: var(--accent-green); color: white; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; text-align: center;">
            {{ session('success') }}
        </div>
    @endif

    @if(count($cart) > 0)
        <div class="cart-container">
            {{-- Lista de Itens --}}
            <div class="cart-items">
                @foreach($cart as $id => $item)
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-title">{{ $item['name'] }}</div>
                            <div class="cart-item-price">R$ {{ number_format($item['price'], 2, ',', '.') }}</div>
                        </div>

                        <div class="cart-controls">
                            {{-- Botão Diminuir/Remover --}}
                            <form action="{{ route('carrinho.remove', $id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-qty">-</button>
                            </form>

                            <span class="qty-number">{{ $item['quantity'] }}</span>

                            {{-- Botão Aumentar --}}
                            <form action="{{ route('carrinho.add', $id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-qty">+</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Resumo e Finalização --}}
            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span>Total:</span>
                    <span class="cart-summary-total">R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>

                <a href="#" class="btn-checkout">Finalizar Pedido</a>

                <form action="{{ route('carrinho.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-clear">Esvaziar carrinho</button>
                </form>
            </div>
        </div>
    @else
        <div class="cart-empty">
            <p>Seu carrinho está vazio no momento.</p>
            <a href="{{ route('products.index') }}" class="btn-back">Ver Cardápio</a>
        </div>
    @endif
</x-app-layout>
