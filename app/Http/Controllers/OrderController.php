<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Processa a criação do pedido (com suporte a local/mesa ou entrega)
    public function store(Request $request)
    {
        $userId = Auth::id();

        // Busca o carrinho do BANCO (mesma fonte usada pelo CartController),
        // não da sessão.
        $cartItems = CartItem::with('product')
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'Seu carrinho está vazio.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'tipo_pedido' => 'required|in:local,entrega',
            'address' => 'required_if:tipo_pedido,entrega|nullable|string',
            'customer_complement' => 'nullable|string',
            'payment_method' => 'required|string',
        ]);

        // Formata o local ou endereço para salvar na tabela
        $localOuEndereco = $request->tipo_pedido === 'entrega'
            ? $request->address . ($request->customer_complement ? ' (Ref: ' . $request->customer_complement . ')' : '')
            : 'Retirada no Local';

        DB::beginTransaction();

        try {
            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Cria o pedido vinculado ao ID do cliente logado
            $order = Order::create([
                'user_id' => $userId,
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'address' => $localOuEndereco,
                'payment_method' => $request->payment_method,
                'total' => $total,
                'status' => 'pendente',
            ]);

            // Insere os itens do pedido
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // Limpa o carrinho do cliente no banco
            CartItem::where('user_id', $userId)->delete();

            DB::commit();

            return redirect()->route('products.index')->with('success', 'Pedido realizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erro ao finalizar o pedido. Tente novamente.');
        }
    }

    // Painel de Pedidos da Cozinha
    // Mostra apenas pedidos que ainda estão em andamento
    // (esconde os já finalizados: entregues ou cancelados)
    public function index()
    {
        $orders = Order::with(['user', 'items.product'])
            ->whereNotIn('status', ['entregue', 'cancelado'])
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    // Atualização de Status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pendente,em_preparo,pronto,entregue',
        ]);
        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status do pedido atualizado!');
    }
}
