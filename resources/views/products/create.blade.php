<link rel="stylesheet" href="{{ asset('css/products.css') }}">

<x-app-layout>
    <x-slot name="header">
        <h2 class="cardapio-header-title">
            {{ __('Cadastrar Novo Produto') }}
        </h2>
    </x-slot>

    <div class="cardapio-container">
        <div class="cardapio-wrapper form-wrapper">

            @if(session('success'))
                <div class="alert-success" role="alert">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="category-block">
                <h3 class="category-title">
                    Informações do Produto
                </h3>

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="category_id" class="form-label">Categoria:</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Selecione uma categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">Nome do Produto:</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Descrição:</label>
                        <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="price" class="form-label">Preço (R$):</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label">Foto do Produto:</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" class="btn-submit btn-add-cart">
                        Salvar Produto
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

@if(session('success'))
    <div id="success-alert" class="alert-success" role="alert">
        <span>{{ session('success') }}</span>
    </div>

    <script>
        setTimeout(function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    </script>
@endif
