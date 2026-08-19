<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Exibe a página do carrinho
    public function index()
    {
        $cart = session()->get('cart', []);

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    // Adiciona um produto ao carrinho
    public function add(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
    }

    // Remove ou diminui a quantidade de um produto
    public function remove(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            if ($cart[$product->id]['quantity'] > 1) {
                $cart[$product->id]['quantity']--;
            } else {
                unset($cart[$product->id]);
            }
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Carrinho atualizado!');
    }

    // Limpa todo o carrinho
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('carrinho.index')->with('success', 'Carrinho esvaziado!');
    }
}
