<nav>
    <div>
        <a href="{{ route('products.index') }}" class="nav-brand">
            <strong>Cantinho da Cerveja</strong>
        </a>
    </div>

    <ul>
        {{-- Páginas Públicas (Sempre visíveis) --}}
        <li>
            <a href="{{ route('products.index') }}">Cardápio</a>
        </li>
        <li>
            <a href="{{ route('carrinho.index') }}">🛒 Carrinho</a>
        </li>

        @auth
            {{-- Páginas Exclusivas para Funcionários e Chefe --}}
            <li>
                <a href="{{ route('orders.index') }}">Pedidos Cozinha</a>
            </li>

            {{-- Identificação do Usuário Logado --}}
            <li class="nav-user-info">
                <span> {{ Auth::user()->name }}</span>
            </li>

            {{-- Botão Desconectar --}}
            <li>
                <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
                    @csrf
                    <button type="submit" class="btn-logout">Sair</button>
                </form>
            </li>
        @else
            {{-- Apenas para Clientes (Deslogados) --}}
            <li>
                <a href="{{ route('login') }}" class="btn-login">Entrar</a>
            </li>
        @endauth
    </ul>
</nav>
