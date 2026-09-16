<link rel="stylesheet" href="{{ asset('css/employees.css') }}">

<x-app-layout>
    <x-slot name="header">
        <h2 class="cardapio-header-title">Cadastrar Funcionário</h2>
    </x-slot>

    <div class="employees-container">

        @if (session('success'))
            <div class="employees-alert-success">{{ session('success') }}</div>
        @endif

        <div class="employees-panel">
            <h3 class="employees-panel-title">Novo Funcionário</h3>

            <form action="{{ route('employees.store') }}" method="POST" class="employees-form">
                @csrf

                <div class="employees-form-group">
                    <label for="name">Nome Completo *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="employees-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="employees-form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="employees-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="employees-form-group">
                    <label for="phone">Telefone / WhatsApp *</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                        <span class="employees-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="employees-form-group">
                    <label for="password">Senha *</label>
                    <input type="password" name="password" id="password" required>
                    @error('password')
                        <span class="employees-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="employees-form-group">
                    <label for="password_confirmation">Confirmar Senha *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                </div>

                <button type="submit" class="employees-submit-btn">Cadastrar Funcionário</button>
            </form>
        </div>

        <div class="employees-panel">
            <h3 class="employees-panel-title">Funcionários Cadastrados</h3>
            <div class="employees-table-wrapper">
                <table class="employees-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($funcionarios as $funcionario)
                            <tr>
                                <td>{{ $funcionario->name }}</td>
                                <td>{{ $funcionario->email }}</td>
                                <td>{{ $funcionario->phone }}</td>
                                <td class="employees-actions-cell">
                                    <form action="{{ route('employees.destroy', $funcionario->id) }}" method="POST"
                                        onsubmit="return confirm('Remover o acesso de {{ $funcionario->name }} ao sistema?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="employees-remove-btn">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="employees-empty">Nenhum funcionário cadastrado ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
