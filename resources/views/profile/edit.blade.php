<x-app-layout>
    <x-slot name="header">
        <h1>Meu Perfil</h1>
    </x-slot>

    <div style="max-width: 600px; margin: 2rem auto; padding: 0 1rem; display: flex; flex-direction: column; gap: 2rem;">

        {{-- Bloco 1: Atualizar Informações do Perfil --}}
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow);">
            <h2 style="font-size: 1.2rem; margin-bottom: 0.5rem;">Informações da Conta</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Atualize o seu nome de usuário e endereço de e-mail.</p>

            <form method="post" action="{{ route('profile.update') }}" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                @method('patch')

                <div>
                    <label for="name" style="display: block; font-weight: bold; margin-bottom: 0.35rem;">Nome</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-main); color: var(--text-main);">
                    @error('name') <span style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" style="display: block; font-weight: bold; margin-bottom: 0.35rem;">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-main); color: var(--text-main);">
                    @error('email') <span style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <button type="submit" style="background: var(--accent-green); color: white; border: none; padding: 0.75rem 1.25rem; font-weight: bold; border-radius: 6px; cursor: pointer; align-self: flex-start; margin-top: 0.5rem;">
                    Salvar Alterações
                </button>
            </form>
        </div>

        {{-- Bloco 2: Atualizar Senha --}}
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow);">
            <h2 style="font-size: 1.2rem; margin-bottom: 0.5rem;">Alterar Senha</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Garanta que sua conta esteja usando uma senha longa e segura.</p>

            <form method="post" action="{{ route('password.update') }}" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" style="display: block; font-weight: bold; margin-bottom: 0.35rem;">Senha Atual</label>
                    <input type="password" id="current_password" name="current_password" style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-main); color: var(--text-main);">
                    @error('current_password', 'updatePassword') <span style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" style="display: block; font-weight: bold; margin-bottom: 0.35rem;">Nova Senha</label>
                    <input type="password" id="password" name="password" style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-main); color: var(--text-main);">
                    @error('password', 'updatePassword') <span style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password_confirmation" style="display: block; font-weight: bold; margin-bottom: 0.35rem;">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-main); color: var(--text-main);">
                    @error('password_confirmation', 'updatePassword') <span style="color: #ef4444; font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <button type="submit" style="background: var(--accent-green); color: white; border: none; padding: 0.75rem 1.25rem; font-weight: bold; border-radius: 6px; cursor: pointer; align-self: flex-start; margin-top: 0.5rem;">
                    Atualizar Senha
                </button>
            </form>
        </div>

        {{-- Bloco 3: Logout / Sair da Conta --}}
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow); text-align: center;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background: #ef4444; color: white; border: none; padding: 0.75rem 1.5rem; font-weight: bold; border-radius: 6px; cursor: pointer; width: 100%;">
                    Sair da Conta (Logout)
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
