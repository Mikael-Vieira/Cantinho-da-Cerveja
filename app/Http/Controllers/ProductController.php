<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Exibe o cardápio/mostruário público de produtos para os clientes.
     */
    public function index()
    {
        // Busca apenas as categorias ativas e carrega os produtos ativos associados a elas
        $categories = Category::where('is_active', true)
            ->with(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return view('products.index', compact('categories'));
    }

    /**
     * Exibe os detalhes de um produto específico (caso clique em ver detalhes).
     */
    public function show(Product $product)
    {
        // Se o produto estiver inativo no banco, retorna erro 404 (Não Encontrado)
        if (! $product->is_active) {
            abort(404);
        }

        return view('products.show', compact('product'));
    }
}
