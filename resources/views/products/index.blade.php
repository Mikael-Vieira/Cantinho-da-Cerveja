<link rel="stylesheet" href="{{ asset('css/products.css') }}">

<x-app-layout>
    <x-slot name="header">
        <h2 class="cardapio-header-title">
            {{ __('Cardápio - Cantinho da Cerveja') }}
        </h2>
    </x-slot>

    <div class="cardapio-container">
        <div class="cardapio-wrapper">

            @if (session('success'))
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

                                @if (!empty($product->image))
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                        class="product-image">
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
                                    <button type="button" class="btn-add-cart"
                                        onclick="openQuantityModal('{{ $product->id }}', '{{ addslashes($product->name) }}', '{{ $product->price }}')">
                                        Adicionar ao Carrinho
                                    </button>
                                </div>

                                <!-- Ações exclusivas do Chefe (Admin): Editar e Excluir -->
                                @auth
                                    @if (auth()->user()->role === 'admin')
                                        <div class="product-admin-actions">
                                            <button type="button" class="btn-edit-product" title="Editar produto"
                                                aria-label="Editar produto"
                                                data-category-id="{{ trim((string) $product->category_id) }}"
                                                data-name="{{ $product->name }}"
                                                data-description="{{ $product->description }}"
                                                data-price="{{ $product->price }}"
                                                data-update-url="{{ route('products.update', $product) }}"
                                                onclick="openEditModal(this)">
                                                ✏️
                                            </button>

                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                class="product-delete-form"
                                                onsubmit="return confirm('Tem certeza que deseja excluir o produto \'{{ addslashes($product->name) }}\'?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-delete-product" title="Excluir produto"
                                                    aria-label="Excluir produto">
                                                    ✕
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth

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
                        <input type="number" name="quantity" id="quantity-input" value="1" min="1"
                            max="99">
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

    <!-- MODAL DE EDITAR PRODUTO (APENAS ADMIN) -->
    @auth
        @if (auth()->user()->role === 'admin')
            <div id="editProductModal" class="modal-overlay" style="display: none;">
                <div class="modal-content">
                    <h3 class="modal-title">Editar Produto</h3>

                    @if ($errors->any())
                        <div class="alert-error">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="#" method="POST" id="edit-product-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="edit-category-id" class="form-label">Categoria</label>
                            <select name="category_id" id="edit-category-id" class="form-control" required>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit-name" class="form-label">Nome</label>
                            <input type="text" name="name" id="edit-name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-description" class="form-label">Descrição</label>
                            <textarea name="description" id="edit-description" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit-price" class="form-label">Preço (R$)</label>
                            <input type="number" name="price" id="edit-price" step="0.01" min="0"
                                class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-image" class="form-label">Nova Imagem (opcional)</label>
                            <input type="file" name="image" id="edit-image"
                                accept="image/jpeg,image/png,image/jpg,image/webp" class="form-control form-control-file">
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancelar</button>
                            <button type="submit" class="btn-confirm">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openEditModal(button) {
                    const modal = document.getElementById('editProductModal');
                    const form = document.getElementById('edit-product-form');
                    const categorySelect = document.getElementById('edit-category-id');
                    const nameInput = document.getElementById('edit-name');
                    const descriptionInput = document.getElementById('edit-description');
                    const priceInput = document.getElementById('edit-price');
                    const imageInput = document.getElementById('edit-image');

                    form.action = button.dataset.updateUrl || '#';

                    // Seleciona a categoria correspondente no <select>
                    const targetCategory = String(button.dataset.categoryId || '').trim();
                    categorySelect.value = targetCategory;
                    if (categorySelect.value !== targetCategory) {
                        for (const option of categorySelect.options) {
                            if (String(option.value).trim() === targetCategory) {
                                option.selected = true;
                                break;
                            }
                        }
                    }

                    nameInput.value = button.dataset.name ?? '';
                    descriptionInput.value = button.dataset.description ?? '';
                    priceInput.value = button.dataset.price ?? '';
                    if (imageInput) {
                        imageInput.value = '';
                    }

                    modal.style.display = 'flex';
                }

                function closeEditModal() {
                    document.getElementById('editProductModal').style.display = 'none';
                }
            </script>
        @endif
    @endauth

    <!-- SCRIPT PURO PARA CONTROLAR O MODAL DE QUANTIDADE -->
    <script>
        function openQuantityModal(id, name, price) {
            document.getElementById('modal-product-id').value = id;
            document.getElementById('modal-product-name').innerText = name;
            document.getElementById('modal-product-price').innerText = 'R$ ' + parseFloat(price).toFixed(2).replace('.',
                ',');
            document.getElementById('quantity-input').value = 1;

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
