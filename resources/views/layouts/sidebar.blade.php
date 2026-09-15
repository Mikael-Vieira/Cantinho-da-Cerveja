<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

<aside class="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-brand">
            <a href="{{ route('products.index') }}">Cantinho da Cerveja</a>
            <span class="sidebar-role-tag">
                {{ Auth::user()->role === 'admin' ? 'Painel do Chefe' : 'Painel do Funcionário' }}
            </span>
        </div>

        <nav class="sidebar-nav">

            <a href="{{ route('carrinho.index') }}" class="sidebar-block-link {{ request()->routeIs('carrinho.*') ? 'is-active' : '' }}">
                Carrinho
            </a>

            @if (Auth::user()->role === 'admin')
                <details class="sidebar-group" {{ request()->routeIs('products.create') || request()->routeIs('categories.create') ? 'open' : '' }}>
                    <summary>Cadastros</summary>
                    <div class="sidebar-group-links">
                        <a href="{{ route('products.create') }}" class="{{ request()->routeIs('products.create') ? 'is-active' : '' }}">
                            Cadastrar Produto
                        </a>
                        <a href="{{ route('categories.create') }}" class="{{ request()->routeIs('categories.create') ? 'is-active' : '' }}">
                            Cadastrar Categoria
                        </a>
                    </div>
                </details>
            @endif

            <details class="sidebar-group" {{ request()->routeIs('products.index') || request()->routeIs('orders.index') ? 'open' : '' }}>
                <summary>Produtos</summary>
                <div class="sidebar-group-links">
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'is-active' : '' }}">
                        Cardápio
                    </a>
                    <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.index') ? 'is-active' : '' }}">
                        Painel da Cozinha
                    </a>
                </div>
            </details>

            @if (Auth::user()->role === 'admin')
                <details class="sidebar-group" {{ request()->routeIs('chefe.dashboard') ? 'open' : '' }}>
                    <summary>Histórico / Relatórios</summary>
                    <div class="sidebar-group-links">
                        <a href="{{ route('chefe.dashboard') }}" class="{{ request()->routeIs('chefe.dashboard') ? 'is-active' : '' }}">
                            Dashboard
                        </a>
                    </div>
                </details>
            @endif

        </nav>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-user">{{ Auth::user()->name }}</div>
        <a href="{{ route('profile.edit') }}" class="sidebar-link">Perfil</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout">Sair</button>
        </form>
    </div>
</aside>
