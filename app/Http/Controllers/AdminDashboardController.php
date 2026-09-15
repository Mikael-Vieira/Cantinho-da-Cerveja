<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Status que contam como faturamento de verdade (vendas concluídas).
     */
    private const STATUS_FATURAMENTO = 'entregue';

    /**
     * Status que aparecem no histórico do dashboard (pedidos "encerrados",
     * seja porque foram entregues ou porque foram cancelados).
     */
    private const STATUS_HISTORICO = ['entregue', 'cancelado'];

    public function index(Request $request)
    {
        $inicioMes = now()->startOfMonth();
        $fimMes = now()->endOfMonth();

        // ------------------------------------------------------------
        // Cards de resumo e gráficos: contam SÓ pedidos entregues
        // (cancelado não é venda real, não deve entrar no faturamento)
        // ------------------------------------------------------------
        $totalFaturado = Order::where('status', self::STATUS_FATURAMENTO)->sum('total');
        $totalPedidos = Order::where('status', self::STATUS_FATURAMENTO)->count();

        $faturadoMes = Order::where('status', self::STATUS_FATURAMENTO)
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->sum('total');

        $pedidosMes = Order::where('status', self::STATUS_FATURAMENTO)
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->count();

        $ticketMedio = $totalPedidos > 0 ? $totalFaturado / $totalPedidos : 0;

        $faturamentoPorDiaRaw = Order::selectRaw('DATE(created_at) as dia, SUM(total) as total')
            ->where('status', self::STATUS_FATURAMENTO)
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('dia')
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        $labelsDias = [];
        $valoresDias = [];
        for ($i = 29; $i >= 0; $i--) {
            $data = now()->subDays($i)->format('Y-m-d');
            $labelsDias[] = now()->subDays($i)->format('d/m');
            $valoresDias[] = isset($faturamentoPorDiaRaw[$data]) ? (float) $faturamentoPorDiaRaw[$data]->total : 0;
        }

        $produtosMaisVendidos = OrderItem::selectRaw('product_id, SUM(quantity) as total_quantidade, SUM(price * quantity) as total_faturado')
            ->whereHas('order', function ($query) {
                $query->where('status', self::STATUS_FATURAMENTO);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_quantidade')
            ->limit(5)
            ->get();

        $labelsProdutos = $produtosMaisVendidos->map(fn ($item) => $item->product->name ?? 'Produto removido');
        $valoresProdutos = $produtosMaisVendidos->map(fn ($item) => (int) $item->total_quantidade);

        // ------------------------------------------------------------
        // Histórico: mostra entregues + cancelados, com filtros
        // ------------------------------------------------------------
        $pedidos = $this->pedidosFiltrados($request)->paginate(15)->withQueryString();

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

    /**
     * Monta a query do histórico já com os filtros aplicados.
     * Reaproveitada pela tela (paginada) e pela exportação CSV (sem paginação).
     */
    private function pedidosFiltrados(Request $request)
    {
        $query = Order::whereIn('status', self::STATUS_HISTORICO)
            ->with(['user', 'items.product']);

        if ($request->filled('cliente')) {
            $query->where('customer_name', 'like', '%' . $request->input('cliente') . '%');
        }

        if ($request->filled('produto')) {
            $nomeProduto = $request->input('produto');
            $query->whereHas('items.product', function ($productQuery) use ($nomeProduto) {
                $productQuery->where('name', 'like', '%' . $nomeProduto . '%');
            });
        }

        if ($request->filled('data')) {
            $query->whereDate('created_at', $request->input('data'));
        }

        if ($request->filled('status') && in_array($request->input('status'), self::STATUS_HISTORICO, true)) {
            $query->where('status', $request->input('status'));
        }

        return $query->latest();
    }

    /**
     * Marca um pedido (entregue ou cancelado) como cancelado.
     */
    public function cancelar(Order $order)
    {
        $order->update(['status' => 'cancelado']);

        return redirect()->back()->with('success', "Pedido #{$order->id} marcado como cancelado.");
    }

    /**
     * Reabre um pedido do histórico, mandando ele de volta pro painel da cozinha.
     */
    public function reabrir(Order $order)
    {
        $order->update(['status' => 'em_preparo']);

        return redirect()->back()->with('success', "Pedido #{$order->id} voltou para o preparo.");
    }

    /**
     * Exclui definitivamente um pedido do histórico (e seus itens, via cascade).
     */
    public function destroy(Order $order)
    {
        $orderId = $order->id;
        $order->delete();

        return redirect()->back()->with('success', "Pedido #{$orderId} excluído do histórico.");
    }

    /**
     * Exporta o histórico filtrado (mesmos filtros da tela) em CSV.
     */
    public function exportCsv(Request $request)
    {
        $orders = $this->pedidosFiltrados($request)->get();

        $filename = 'relatorio-pedidos-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // BOM no início: garante que o Excel abra os acentos certinho
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Pedido', 'Cliente', 'Telefone', 'Data', 'Valor', 'Forma de Pagamento', 'Status', 'Itens',
            ], ';');

            foreach ($orders as $order) {
                $itens = $order->items->map(function ($item) {
                    $nome = $item->product->name ?? 'Produto removido';
                    return "{$item->quantity}x {$nome}";
                })->implode(' | ');

                fputcsv($handle, [
                    $order->id,
                    $order->customer_name,
                    $order->phone,
                    $order->created_at->format('d/m/Y H:i'),
                    number_format($order->total, 2, ',', '.'),
                    $order->payment_method,
                    ucfirst(str_replace('_', ' ', $order->status)),
                    $itens,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
