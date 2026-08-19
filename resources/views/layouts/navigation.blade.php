<nav>
    <div>
        <a href="{{ route('products.index') }}">
            <strong>Cantinho da Cerveja</strong>
        </a>
    </div>

    <ul>
        <li>
            <a href="{{ route('products.index') }}">Cardápio</a>
        </li>
        <li>
            {{-- Link simples que aponta diretamente para a rota do carrinho --}}
            <a href="{{ route('carrinho.index') }}">🛒 Carrinho</a>
        </li>

        @auth
            <li><a href="{{ route('profile.edit') }}">Perfil</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none; border:none; color:inherit; cursor:pointer;">Sair</button>
                </form>
            </li>
        @else
            <li><a href="{{ route('login') }}">Entrar</a></li>
        @endauth
    </ul>
</nav>
