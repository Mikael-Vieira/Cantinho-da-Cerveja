<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/products.css') }}">
    @endpush

    <x-slot name="header">
        <h1>Nosso Cardápio</h1>
    </x-slot>

    {{-- Popup / Toast Flutuante --}}
    <div id="toast-popup" class="toast-popup">
        <span class="toast-icon">✓</span>
        <span id="toast-message">Produto adicionado ao carrinho!</span>
    </div>

    <section>
        @forelse($categories as $category)
            @if($category->products->isNotEmpty())
                <div class="category-block">
                    <h2 class="category-title">{{ $category->name }}</h2>

                    <div class="product-grid">
                        @foreach($category->products as $product)
                            <article class="product-card">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                @else
                                    <div class="product-image-placeholder">[Sem Foto]</div>
                                @endif

                                <h3 class="product-title">{{ $product->name }}</h3>
                                <p class="product-description">{{ $product->description }}</p>
                                <strong class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</strong>

                                <form action="{{ route('carrinho.add', $product->id) }}" method="POST" class="form-add-product" data-product-name="{{ $product->name }}">
                                    @csrf
                                    <button type="submit" class="btn-add-cart">Adicionar ao Carrinho</button>
                                </form>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <p>Nenhum produto cadastrado no momento.</p>
        @endforelse
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const forms = document.querySelectorAll('.form-add-product');
                const toast = document.getElementById('toast-popup');
                const toastMessage = document.getElementById('toast-message');

                function showToast(message) {
                    toastMessage.textContent = message;
                    toast.classList.add('show');

                    setTimeout(() => {
                        toast.classList.remove('show');
                    }, 3000);
                }

                forms.forEach(form => {
                    form.addEventListener('submit', function (event) {
                        const productName = this.getAttribute('data-product-name');

                        // Pergunta antes de adicionar
                        const confirmed = confirm(`Deseja realmente adicionar "${productName}" ao carrinho?`);

                        if (!confirmed) {
                            event.preventDefault();
                        }
                    });
                });

                // Exibe o popup automaticamente se o Laravel redirecionar com mensagem de sucesso
                @if(session('success'))
                    showToast("{{ session('success') }}");
                @endif
            });
        </script>
    @endpush
</x-app-layout>
