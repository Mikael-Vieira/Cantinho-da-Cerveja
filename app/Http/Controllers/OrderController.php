<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Exibe a tela de checkout
    public function checkout()
    {
        $cartKey = 'cart_' . Auth::id();
        $cart = session()->get($cartKey, []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Seu carrinho está vazio.');
        }

        $total = array_reduce($cart, function ($acc, $item) {
            return $acc + ($item['price'] * $item['quantity']);
        }, 0);

        return view('checkout.index', compact('cart', 'total'));
    }

    // Processa a criação do pedido
    public function store(Request $request)
    {
        $cartKey = 'cart_' . Auth::id();
        $cart = session()->get($cartKey, []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Seu carrinho está vazio.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $total = array_reduce($cart, function ($acc, $item) {
            return $acc + ($item['price'] * $item['quantity']);
        }, 0);

        // Cria o pedido vinculado ao ID do cliente logado
        $order = Order::create([
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'payment_method' => $request->payment_method,
            'total' => $total,
            'status' => 'pending',
        ]);

        // Insere os itens do pedido
        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // Limpa o carrinho do cliente
        session()->forget($cartKey);

        return redirect()->route('products.index')->with('success', 'Pedido realizado com sucesso!');
    }

    // Painel de Pedidos da Cozinha
    public function index()
    {
        $orders = Order::with('items.product')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    // Atualização de Status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|string']);
        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status do pedido atualizado!');
    }
}
