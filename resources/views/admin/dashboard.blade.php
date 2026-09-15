<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<x-app-layout>
    <x-slot name="header">
        <h2 class="cardapio-header-title">Dashboard Financeiro</h2>
    </x-slot>

    <div class="dashboard-container">

        <!-- Cards de Resumo -->
        <div class="dashboard-cards">

            <div class="dashboard-card">
                <p class="dashboard-card-label">Faturamento Total</p>
                <p class="dashboard-card-value is-highlight">R$ {{ number_format($totalFaturado, 2, ',', '.') }}</p>
            </div>

            <div class="dashboard-card">
                <p class="dashboard-card-label">Faturamento Este Mês</p>
                <p class="dashboard-card-value is-highlight">R$ {{ number_format($faturadoMes, 2, ',', '.') }}</p>
            </div>

            <div class="dashboard-card">
                <p class="dashboard-card-label">Pedidos (Total / Mês)</p>
                <p class="dashboard-card-value">
                    {{ $totalPedidos }} <span class="dashboard-card-value-sub">/ {{ $pedidosMes }} este mês</span>
                </p>
            </div>

            <div class="dashboard-card">
                <p class="dashboard-card-label">Ticket Médio</p>
                <p class="dashboard-card-value">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</p>
            </div>

        </div>

        <!-- Gráficos -->
        <div class="dashboard-charts">

            <div class="dashboard-panel">
                <h3 class="dashboard-panel-title">Faturamento — Últimos 30 dias</h3>
                <canvas id="chartFaturamento" height="120"></canvas>
            </div>

            <div class="dashboard-panel">
                <h3 class="dashboard-panel-title">Produtos Mais Vendidos</h3>
                <canvas id="chartProdutos" height="120"></canvas>
            </div>

        </div>

        <!-- Tabela de Ranking de Produtos -->
        <div class="dashboard-panel">
            <h3 class="dashboard-panel-title">Ranking de Produtos (Top 5 em quantidade)</h3>
            <div class="dashboard-table-wrapper">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Qtd. Vendida</th>
                            <th>Faturado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produtosMaisVendidos as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Produto removido' }}</td>
                                <td>{{ $item->total_quantidade }}</td>
                                <td class="is-highlight">R$ {{ number_format($item->total_faturado, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="is-empty">Nenhuma venda registrada ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabela de Pedidos -->
        <div class="dashboard-panel">
            <div class="dashboard-panel-header">
                <h3 class="dashboard-panel-title">Histórico de Pedidos</h3>
                <a href="{{ route('chefe.dashboard.export', request()->query()) }}" class="dashboard-export-btn">
                    ⬇ Exportar CSV
                </a>
            </div>

            <form method="GET" action="{{ route('chefe.dashboard') }}" class="dashboard-filter-form">
                <div class="dashboard-filter-field">
                    <label for="filtro-cliente">Cliente</label>
                    <input type="text" id="filtro-cliente" name="cliente" value="{{ request('cliente') }}" placeholder="Nome do cliente">
                </div>

                <div class="dashboard-filter-field">
                    <label for="filtro-produto">Lanche</label>
                    <input type="text" id="filtro-produto" name="produto" value="{{ request('produto') }}" placeholder="Nome do lanche">
                </div>

                <div class="dashboard-filter-field">
                    <label for="filtro-data">Data</label>
                    <input type="date" id="filtro-data" name="data" value="{{ request('data') }}">
                </div>

                <div class="dashboard-filter-field">
                    <label for="filtro-status">Status</label>
                    <select id="filtro-status" name="status">
                        <option value="">Todos (entregues e cancelados)</option>
                        <option value="entregue" {{ request('status') === 'entregue' ? 'selected' : '' }}>Entregue</option>
                        <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="dashboard-filter-actions">
                    <button type="submit" class="dashboard-filter-btn">Filtrar</button>
                    @if (request('cliente') || request('produto') || request('data') || request('status'))
                        <a href="{{ route('chefe.dashboard') }}" class="dashboard-filter-clear">Limpar filtros</a>
                    @endif
                </div>
            </form>

            <div class="dashboard-table-wrapper">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pedidos as $pedido)
                            <tr>
                                <td>#{{ $pedido->id }}</td>
                                <td>{{ $pedido->customer_name }}</td>
                                <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                <td class="is-highlight">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                                <td>
                                    <span class="dashboard-badge dashboard-badge-{{ $pedido->status }}">
                                        {{ str_replace('_', ' ', $pedido->status) }}
                                    </span>
                                </td>
                                <td class="dashboard-actions-cell">
                                    <details class="dashboard-actions-menu">
                                        <summary>⋮</summary>
                                        <div class="dashboard-actions-menu-content">
                                            @if ($pedido->status !== 'cancelado')
                                                <form action="{{ route('chefe.pedidos.cancelar', $pedido->id) }}" method="POST" onsubmit="return confirm('Marcar o pedido #{{ $pedido->id }} como cancelado?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit">Cancelar pedido</button>
                                                </form>
                                            @endif

                                            <form action="{{ route('chefe.pedidos.reabrir', $pedido->id) }}" method="POST" onsubmit="return confirm('Enviar o pedido #{{ $pedido->id }} de volta para o preparo?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit">Voltar para preparo</button>
                                            </form>

                                            <form action="{{ route('chefe.pedidos.destroy', $pedido->id) }}" method="POST" onsubmit="return confirm('Excluir o pedido #{{ $pedido->id }} definitivamente? Essa ação não pode ser desfeita.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="is-danger">Excluir do histórico</button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="is-empty">Nenhum pedido registrado ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="dashboard-pagination">
                @if ($pedidos->onFirstPage())
                    <span class="is-disabled">&laquo; Anterior</span>
                @else
                    <a href="{{ $pedidos->previousPageUrl() }}">&laquo; Anterior</a>
                @endif

                @for ($page = 1; $page <= $pedidos->lastPage(); $page++)
                    @if ($page === $pedidos->currentPage())
                        <span class="is-current">{{ $page }}</span>
                    @else
                        <a href="{{ $pedidos->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                @if ($pedidos->hasMorePages())
                    <a href="{{ $pedidos->nextPageUrl() }}">Próxima &raquo;</a>
                @else
                    <span class="is-disabled">Próxima &raquo;</span>
                @endif
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        const labelsDias = @json($labelsDias);
        const valoresDias = @json($valoresDias);
        const labelsProdutos = @json($labelsProdutos);
        const valoresProdutos = @json($valoresProdutos);

        new Chart(document.getElementById('chartFaturamento'), {
            type: 'line',
            data: {
                labels: labelsDias,
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: valoresDias,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.15)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#a1a1aa' } },
                    x: { ticks: { color: '#a1a1aa', maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } }
                }
            }
        });

        new Chart(document.getElementById('chartProdutos'), {
            type: 'bar',
            data: {
                labels: labelsProdutos,
                datasets: [{
                    label: 'Qtd. vendida',
                    data: valoresProdutos,
                    backgroundColor: '#fbbf24',
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { color: '#a1a1aa' } },
                    y: { ticks: { color: '#a1a1aa' } }
                }
            }
        });
    </script>
</x-app-layout>
