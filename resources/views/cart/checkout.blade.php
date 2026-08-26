<x-app-layout>
    <x-slot name="header">
        <h1>Finalizar Pedido</h1>
    </x-slot>

    <div style="max-width: 600px; margin: 0 auto; padding: 1rem;">
        <form action="{{ route('checkout.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf

            <div>
                <label for="customer_name" style="display:block; margin-bottom: 0.5rem; font-weight: bold;">Nome Completo *</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', Auth::user()->name) }}" required style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-card); color: var(--text-main);">
            </div>

            <div>
                <label for="phone" style="display:block; margin-bottom: 0.5rem; font-weight: bold;">Telefone / WhatsApp *</label>
                <input type="tel" name="phone" id="phone" placeholder="(28) 99999-9999" value="{{ old('phone') }}" required style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-card); color: var(--text-main);">
            </div>

            <div>
                <label for="address" style="display:block; margin-bottom: 0.5rem; font-weight: bold;">Endereço ou Nº da Mesa *</label>
                <input type="text" name="address" id="address" placeholder="Ex: Rua Principal, 123 ou Mesa 05" value="{{ old('address') }}" required style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-card); color: var(--text-main);">
            </div>

            <div>
                <label for="payment_method" style="display:block; margin-bottom: 0.5rem; font-weight: bold;">Forma de Pagamento *</label>
                <select name="payment_method" id="payment_method" required style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-card); color: var(--text-main);">
                    <option value="" disabled selected>Selecione uma opção</option>
                    <option value="Pix">PIX</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Cartão de Débito">Cartão de Débito</option>
                    <option value="Dinheiro">Dinheiro</option>
                </select>
            </div>

            <div>
                <label for="customer_complement" style="display:block; margin-bottom: 0.5rem; font-weight: bold;">Informação Complementar (Opcional)</label>
                <textarea name="customer_complement" id="customer_complement" rows="2" placeholder="Ex: Apto 201, Ponto de referência, Sem cebola..." style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--border-subtle); background: var(--bg-card); color: var(--text-main);"></textarea>
            </div>

            <div style="background: var(--bg-card); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-subtle); margin-top: 1rem;">
                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1rem;">
                    <span>Total a Pagar:</span>
                    <span style="color: var(--accent-green);">R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>
            </div>

            <button type="submit" style="background-color: var(--accent-green); color: white; border: none; padding: 1rem; font-size: 1.1rem; font-weight: bold; border-radius: 8px; cursor: pointer; margin-top: 1rem;">
                Confirmar e Enviar Pedido
            </button>
        </form>
    </div>
</x-app-layout>
