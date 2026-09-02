<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Exibe a página do carrinho com os itens do banco e o total calculado.
     */
    public function index()
    {
        $userId = Auth::id();

        // Busca os itens do banco de dados vinculados ao usuário logado, junto com os dados do produto
        $cartItems = CartItem::with('product')
            ->where('user_id', $userId)
            ->get();

        $total = 0;
        $cart = [];

        // Monta a estrutura para manter compatibilidade com a view
        foreach ($cartItems as $item) {
            if ($item->product) {
                $cart[$item->product_id] = [
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'image' => $item->product->image,
                ];
                $total += $item->product->price * $item->quantity;
            }
        }

        return view('cart.index', compact('cart', 'total'));
    }

    /**
     * Adiciona um produto ao carrinho do cliente logado no banco de dados.
     */
    public function add(Request $request, Product $product)
    {
        $userId = Auth::id();
        $quantity = max(1, (int) $request->input('quantity', 1));

        // Procura se o usuário já tem esse produto no carrinho
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Se já tem, soma a quantidade
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Se não tem, cria um novo registro na tabela
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
    }

    /**
     * Remove ou diminui a quantidade de um produto no banco.
     */
    public function remove(Request $request, Product $product)
    {
        $userId = Auth::id();

        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            if ($cartItem->quantity > 1) {
                $cartItem->quantity--;
                $cartItem->save();
            } else {
                $cartItem->delete();
            }
        }

        return redirect()->back()->with('success', 'Carrinho atualizado!');
    }

    /**
     * Esvazia todo o carrinho do cliente logado no banco.
     */
    public function clear()
    {
        $userId = Auth::id();

        CartItem::where('user_id', $userId)->delete();

        return redirect()->route('carrinho.index')->with('success', 'Carrinho esvaziado!');
    }
}
