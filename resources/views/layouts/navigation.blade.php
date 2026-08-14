<nav>
    <div>
        {{-- Logo / Nome da Loja --}}
        <a href="{{ route('products.index') }}">
            <strong>Sistema de Pedidos</strong>
        </a>
    </div>

    <ul>
        <li>
            <a href="{{ route('products.index') }}">Cardápio</a>
        </li>
        <li>
            <a href="{{ route('carrinho.index') }}">Carrinho</a>
        </li>

        @auth
            {{-- Links para Usuario Logado --}}
            <li>
                <a href="{{ route('cliente.pedidos') }}">Meus Pedidos</a>
            </li>
            <li>
                <span>Olá, {{ Auth::user()->name }}</span>
            </li>
            <li>
                <a href="{{ route('profile.edit') }}">Perfil</a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Sair</button>
                </form>
            </li>
        @else
            {{-- Links para Visitante --}}
            <li>
                <a href="{{ route('login') }}">Entrar</a>
            </li>
            <li>
                <a href="{{ route('register') }}">Cadastrar</a>
            </li>
        @endauth
    </ul>
</nav>
