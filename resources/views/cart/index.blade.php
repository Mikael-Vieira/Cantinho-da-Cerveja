<link rel="stylesheet" href="{{ asset('css/cart.css') }}">

<x-app-layout>
    <x-slot name="header">
        <h2 class="cardapio-header-title">
            {{ __('Meu Carrinho') }}
        </h2>
    </x-slot>

    <div class="cardapio-container">
        <div class="cardapio-wrapper">

            @if (session('success'))
                <div class="alert-success" role="alert">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-success" role="alert" style="background-color: #7f1d1d;">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (empty($cart))
                <div class="cart-empty">
                    <p>Seu carrinho está vazio.</p>
                    <a href="{{ route('products.index') }}" class="btn-back">Ver Cardápio</a>
                </div>
            @else
                <div class="cart-container">

                    <!-- Lista de Itens -->
                    <div class="cart-items">
                        @foreach ($cart as $id => $item)
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

                        <!-- Botão que abre o modal -->
                        <button type="button" id="btn-open-checkout" class="btn-checkout">
                            Finalizar Pedido
                        </button>

                        <form action="{{ route('carrinho.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-clear">Esvaziar Carrinho</button>
                        </form>
                    </div>

                </div>
            @endif

        </div>
    </div>

    <!-- ============================= -->
    <!-- MODAL DE FINALIZAÇÃO DE PEDIDO -->
    <!-- ============================= -->
    <div id="checkout-modal-overlay" class="modal-overlay {{ $errors->any() ? 'is-open' : '' }}">
        <div class="modal-box" id="checkout-modal-box">
            <button type="button" class="modal-close" id="btn-close-checkout" aria-label="Fechar">&times;</button>

            <h3 class="modal-title">Finalizar Pedido</h3>

            @if ($errors->any())
                <div style="background: #7f1d1d; color: #fecaca; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem;">
                    <strong>Corrija os campos abaixo:</strong>
                    <ul style="margin: 0.5rem 0 0 1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST" class="modal-form">
                @csrf

                <div class="form-group">
                    <label for="customer_name">Nome Completo *</label>
                    <input type="text" name="customer_name" id="customer_name"
                        value="{{ old('customer_name', Auth::user()->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="phone">Telefone / WhatsApp *</label>
                    <input type="tel" name="phone" id="phone"
                        value="{{ old('phone', Auth::user()->phone ?? '') }}" required>
                </div>

                <!-- Escolha: Local ou Entrega -->
                <div class="form-group">
                    <label>Onde será o pedido? *</label>
                    <div class="radio-row">
                        <label class="radio-option">
                            <input type="radio" name="tipo_pedido" value="local" id="tipo_local" checked>
                            <span>Retirar no Local</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="tipo_pedido" value="entrega" id="tipo_entrega">
                            <span>Delivery / Entrega</span>
                        </label>
                    </div>
                </div>

                <!-- Se for Entrega: Endereço e Complemento -->
                <div id="campos-entrega" class="conditional-fields" hidden>
                    <div class="form-group">
                        <label for="address">Endereço de Entrega *</label>
                        <input type="text" name="address" id="address"
                            value="{{ old('address', Auth::user()->address ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="customer_complement">Complemento / Referência</label>
                        <textarea name="customer_complement" id="customer_complement" rows="2"
                            placeholder="Ex: Perto da praça, portão azul...">{{ old('customer_complement') }}</textarea>
                    </div>
                </div>

                <!-- Se for Local: pedido é pra retirar no balcão, sem info extra -->
                <div id="campos-local" class="conditional-fields">
                    <p style="color: #a1a1aa; font-size: 0.85rem; margin: 0;">
                        Seu pedido ficará pronto para retirada no local.
                    </p>
                </div>

                <!-- Forma de Pagamento -->
                <div class="form-group">
                    <label for="payment_method">Forma de Pagamento *</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>Selecione uma opção</option>
                        <option value="Pix" {{ old('payment_method') === 'Pix' ? 'selected' : '' }}>PIX</option>
                        <option value="Cartão de Crédito" {{ old('payment_method') === 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="Cartão de Débito" {{ old('payment_method') === 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                        <option value="Dinheiro" {{ old('payment_method') === 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                    </select>
                </div>

                <!-- Botões do Modal -->
                <div class="modal-actions">
                    <button type="button" class="btn-modal-cancel" id="btn-cancel-checkout">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-modal-confirm">
                        Confirmar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/cart.js') }}"></script>
</x-app-layout>
