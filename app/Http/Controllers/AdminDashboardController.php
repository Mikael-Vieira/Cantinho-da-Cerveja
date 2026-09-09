<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $inicioMes = now()->startOfMonth();
        $fimMes = now()->endOfMonth();

        // O dashboard só considera pedidos já FINALIZADOS (status = entregue).
        // Pedidos ainda em andamento (pendente, em_preparo, pronto) não entram
        // nas métricas, porque ainda podem ser cancelados/alterados na cozinha.
        $statusFinalizado = 'entregue';

        // Cards de resumo
        $totalFaturado = Order::where('status', $statusFinalizado)->sum('total');
        $totalPedidos = Order::where('status', $statusFinalizado)->count();

        $faturadoMes = Order::where('status', $statusFinalizado)
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->sum('total');

        $pedidosMes = Order::where('status', $statusFinalizado)
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->count();

        $ticketMedio = $totalPedidos > 0 ? $totalFaturado / $totalPedidos : 0;

        // Faturamento por dia (últimos 30 dias) — para o gráfico de linha
        $faturamentoPorDiaRaw = Order::selectRaw('DATE(created_at) as dia, SUM(total) as total')
            ->where('status', $statusFinalizado)
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        // Preenche os dias sem venda com 0, para o gráfico não ficar "quebrado"
        $labelsDias = [];
        $valoresDias = [];
        for ($i = 29; $i >= 0; $i--) {
            $data = now()->subDays($i)->format('Y-m-d');
            $labelsDias[] = now()->subDays($i)->format('d/m');
            $valoresDias[] = isset($faturamentoPorDiaRaw[$data]) ? (float) $faturamentoPorDiaRaw[$data]->total : 0;
        }

        // Ranking de produtos mais vendidos (top 5 por quantidade)
        // Considera apenas itens de pedidos já finalizados
        $produtosMaisVendidos = OrderItem::selectRaw('product_id, SUM(quantity) as total_quantidade, SUM(price * quantity) as total_faturado')
            ->whereHas('order', function ($query) use ($statusFinalizado) {
                $query->where('status', $statusFinalizado);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_quantidade')
            ->limit(5)
            ->get();

        $labelsProdutos = $produtosMaisVendidos->map(fn ($item) => $item->product->name ?? 'Produto removido');
        $valoresProdutos = $produtosMaisVendidos->map(fn ($item) => (int) $item->total_quantidade);

        // Tabela detalhada de pedidos (com paginação) — apenas finalizados
        $pedidos = Order::where('status', $statusFinalizado)
            ->with('user')
            ->latest()
            ->paginate(15);

        return view('admin.dashboard', compact(
            'totalFaturado',
            'totalPedidos',
            'faturadoMes',
            'pedidosMes',
            'ticketMedio',
            'labelsDias',
            'valoresDias',
            'produtosMaisVendidos',
            'labelsProdutos',
            'valoresProdutos',
            'pedidos'
        ));
    }
}
