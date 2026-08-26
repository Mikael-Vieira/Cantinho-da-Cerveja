<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Exibe a lista de produtos no cardápio.
     */
    public function index()
    {
        // Busca as categorias ativas e traz os produtos relacionados de cada uma
        $categories = Category::with('products')->get();

        return view('products.index', compact('categories'));
    }

    /**
     * Mostra o formulário para criar um novo produto (se houver painel administrativo).
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Salva um novo produto no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return redirect()->route('products.index')->with('success', 'Produto cadastrado com sucesso!');
    }
}
