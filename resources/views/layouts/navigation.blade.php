<nav>
    <div>
        <a href="{{ route('products.index') }}" class="nav-brand">
            <strong>Cantinho da Cerveja</strong>
        </a>
    </div>

    <ul>
        {{-- Páginas Básicas (Sempre visíveis para todos logados ou deslogados) --}}
        <li>
            <a href="{{ route('products.index') }}">Cardápio</a>
        </li>

        @auth
            {{-- Carrinho visível para qualquer usuário logado (Cliente, Funcionário, Admin) --}}
            <li>
                <a href="{{ route('carrinho.index') }}">🛒 Carrinho</a>
            </li>

            {{--
              Painel da Cozinha:
              Aparece apenas se o usuário for 'admin' ou 'funcionario'
            --}}
            @if (Auth::user()->role === 'admin' || Auth::user()->role === 'funcionario')
                <li>
                    <a href="{{ route('orders.index') }}">Painel da Cozinha</a>
                </li>
            @endif

            <!-- Link visível apenas para o Admin / Chefe -->
            @if (Auth::user()->role === 'admin')
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('products.create')" :active="request()->routeIs('products.create')">
                        {{ __('Cadastrar Produto') }}
                    </x-nav-link>
                </div>
            @endif

            {{-- Informações e Ações do Usuário --}}
            <li class="nav-user-info">
                <span>{{ Auth::user()->name }}</span>
            </li>
            <li>
                <a href="{{ route('profile.edit') }}">Perfil</a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
                    @csrf
                    <button type="submit" class="btn-logout">Sair</button>
                </form>
            </li>
        @else
            {{-- Ações para visitantes não logados --}}
            <li>
                <a href="{{ route('login') }}" class="btn-login">Entrar</a>
            </li>
            <li>
                <a href="{{ route('register') }}" class="btn-register">Criar Conta</a>
            </li>
        @endauth
    </ul>
</nav>
