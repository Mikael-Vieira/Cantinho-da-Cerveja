<link rel="stylesheet" href="{{ asset('css/cart.css') }}">


<x-app-layout>
    <x-slot name="header">
        <h2 class="cardapio-header-title">
            {{ __('Meu Carrinho') }}
        </h2>
    </x-slot>

    <div class="cardapio-container">
        <div class="cardapio-wrapper">

            @if(session('success'))
                <div class="alert-success" role="alert">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(empty($cart))
                <div class="cart-empty">
                    <p>Seu carrinho está vazio.</p>
                    <a href="{{ route('products.index') }}" class="btn-back">Ver Cardápio</a>
                </div>
            @else
                <div class="cart-container">

                    <!-- Lista de Itens -->
                    <div class="cart-items">
                        @foreach($cart as $id => $item)
                            <div class="cart-item">

                                <div class="cart-item-info">
                                    <h4 class="cart-item-title">{{ $item['name'] }}</h4>
                                    <span class="cart-item-price">
                                        R$ {{ number_format($item['price'], 2, ',', '.') }} un
                                    </span>
                                </div>

                                <!-- Controles de Quantidade -->
                                <div class="cart-controls">
                                    <form action="{{ route('carrinho.remove', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-qty">-</button>
                                    </form>

                                    <span class="qty-number">{{ $item['quantity'] }}</span>

                                    <form action="{{ route('carrinho.add', $id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-qty">+</button>
                                    </form>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Resumo do Pedido -->
                    <div class="cart-summary">
                        <div class="cart-summary-row">
                            <span>Total Geral:</span>
                            <span class="cart-summary-total">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>

                        <a href="#" class="btn-checkout">
                            Concluir Pedido
                        </a>

                        <form action="{{ route('carrinho.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-clear">Esvaziar Carrinho</button>
                        </form>
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-app-layout>
