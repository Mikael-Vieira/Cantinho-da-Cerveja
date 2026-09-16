<link rel="stylesheet" href="{{ asset('css/categoria.css') }}">

<x-app-layout>
    <x-slot name="header">
        <h2 class="cardapio-header-title">Cadastrar Categoria</h2>
    </x-slot>

    <div class="categories-container">

        @if (session('success'))
            <div class="categories-alert-success">{{ session('success') }}</div>
        @endif

        <div class="categories-panel">
            <h3 class="categories-panel-title">Nova Categoria</h3>

            <form action="{{ route('categories.store') }}" method="POST" class="categories-form">
                @csrf

                <div class="categories-form-group">
                    <label for="name">Nome da Categoria *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ex: Lanches, Bebidas, Sobremesas">
                    @error('name')
                        <span class="categories-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="categories-form-checkbox">
                    <label>
                        <input type="checkbox" name="is_active" value="1" checked>
                        Categoria ativa (visível no cardápio)
                    </label>
                </div>

                <button type="submit" class="categories-submit-btn">Salvar Categoria</button>
            </form>
        </div>

        <div class="categories-panel">
            <h3 class="categories-panel-title">Categorias Cadastradas</h3>
            <div class="categories-table-wrapper">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Slug</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categorias as $categoria)
                            <tr>
                                <td>{{ $categoria->name }}</td>
                                <td>{{ $categoria->slug }}</td>
                                <td>
                                    <span class="categories-badge {{ $categoria->is_active ? 'is-active' : 'is-inactive' }}">
                                        {{ $categoria->is_active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="categories-empty">Nenhuma categoria cadastrada ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
