<link rel="stylesheet" href="{{ asset('css/products.css') }}">

<x-app-layout>
    <x-slot name="header">
        <h2 class="cardapio-header-title">
            {{ __('Cardápio - Cantinho da Cerveja') }}
        </h2>
    </x-slot>

    <div class="cardapio-container">
        <div class="cardapio-wrapper">

            @if(session('success'))
                <div class="alert-success" role="alert">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Loop por cada Categoria -->
            @forelse($categories as $category)
                <div class="category-block">
                    <h3 class="category-title">
                        {{ $category->name }}
                    </h3>

                    <div class="product-grid">
                        @forelse($category->products as $product)
                            <div class="product-card" id="product-{{ $product->id }}">

                                @if(!empty($product->image))
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                @else
                                    <div class="product-image-placeholder">
                                        <span>Sem foto</span>
                                    </div>
                                @endif

                                <div class="product-info">
                                    <h4 class="product-title">{{ $product->name }}</h4>
                                    <p class="product-description">{{ $product->description }}</p>
                                </div>

                                <div class="product-footer">
                                    <span class="product-price">
                                        R$ {{ number_format($product->price, 2, ',', '.') }}
                                    </span>

                                    <!-- Botão que chama a função JS passando os dados do produto -->
                                    <button type="button"
                                            class="btn-add-cart"
                                            onclick="openQuantityModal('{{ $product->id }}', '{{ addslashes($product->name) }}', '{{ $product->price }}')">
                                        Adicionar ao Carrinho
                                    </button>
                                </div>

                            </div>
                        @empty
                            <p class="products-empty">Nenhum produto cadastrado nesta categoria.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="products-empty">
                    Nenhuma categoria encontrada.
                </div>
            @endforelse

        </div>
    </div>

    <!-- MODAL DE ESCOLHER QUANTIDADE (CSS PURO) -->
    <div id="quantityModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <h3 id="modal-product-name" class="modal-title">Nome do Produto</h3>
            <p id="modal-product-price" class="modal-price">R$ 0,00</p>

            <form action="#" method="POST" id="modal-form">
                @csrf
                <input type="hidden" name="product_id" id="modal-product-id">

                <div class="modal-quantity-group">
                    <label for="quantity">Quantidade:</label>
                    <div class="quantity-controls">
                        <button type="button" onclick="decrementQuantity()">-</button>
                        <input type="number" name="quantity" id="quantity-input" value="1" min="1" max="99">
                        <button type="button" onclick="incrementQuantity()">+</button>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeQuantityModal()">Cancelar</button>
                    <button type="submit" class="btn-confirm">Confirmar e Adicionar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT PURO PARA CONTROLAR O MODAL -->
    <script>
        function openQuantityModal(id, name, price) {
            document.getElementById('modal-product-id').value = id;
            document.getElementById('modal-product-name').innerText = name;
            document.getElementById('modal-product-price').innerText = 'R$ ' + parseFloat(price).toFixed(2).replace('.', ',');
            document.getElementById('quantity-input').value = 1;

            // Ajuste a rota do form conforme a sua rota de carrinho (ex: /carrinho/adicionar)
            document.getElementById('modal-form').action = '/carrinho/adicionar/' + id;

            document.getElementById('quantityModal').style.display = 'flex';
        }

        function closeQuantityModal() {
            document.getElementById('quantityModal').style.display = 'none';
        }

        function incrementQuantity() {
            let input = document.getElementById('quantity-input');
            input.value = parseInt(input.value) + 1;
        }

        function decrementQuantity() {
            let input = document.getElementById('quantity-input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }
    </script>
</x-app-layout>
