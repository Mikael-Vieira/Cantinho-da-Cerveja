<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Exibe o formulário de cadastro de categoria.
     */
    public function create()
    {
        $categorias = Category::orderBy('name')->get();

        return view('products.categoria', compact('categorias'));
    }

    /**
     * Salva uma nova categoria.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('categories.create')->with('success', 'Categoria cadastrada com sucesso!');
    }
}
