<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Exibe o formulário de Checkout
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('success', 'Seu carrinho está vazio!');
        }

        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        return view('cart.checkout', compact('cart', 'total'));
    }

    // Salva o Pedido no Banco de Dados
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:255',
            'customer_complement' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index');
        }

        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'customer_complement' => $request->customer_complement,
            'total' => $total,
            'status' => 'pendente',
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ]);
        }

        session()->forget('cart');

        return redirect()->route('order.status', $order->id)->with('success', 'Pedido enviado com sucesso!');
    }

    // Status do Pedido para o Cliente
    public function status(Order $order)
    {
        return view('orders.status', compact('order'));
    }

    // Painel dos Funcionários
    public function index()
    {
        // Busca apenas os pedidos ativos da cozinha (oculta os 'entregue')
        $orders = Order::with('items')
            ->whereIn('status', ['pendente', 'em_preparo', 'pronto'])
            ->orderBy('created_at', 'asc') // Exibe os mais antigos primeiro para fila de preparo
            ->get();

        return view('orders.index', compact('orders'));
    }

    // Atualização de Status pelo Funcionário
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pendente,em_preparo,pronto,entregue',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status do pedido #' . $order->id . ' atualizado!');
    }
}
