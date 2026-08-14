<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/products.css') }}">
    @endpush

    <x-slot name="header">
        <h1>Nosso Cardápio</h1>
    </x-slot>

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

                                <form action="#" method="POST">
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
</x-app-layout>
