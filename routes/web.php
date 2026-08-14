<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 1. PÁGINAS PÚBLICAS E ÁREA DO CLIENTE
// Qualquer pessoa pode acessar a Home, o Carrinho e Fazer Pedidos
Route::get('/', function () {
    return view('welcome');
});

Route::get('/carrinho', function () {
    return '<h1>Carrinho de Compras</h1>';
})->name('carrinho.index');

Route::get('/checkout', function () {
    return '<h1>Finalizar Pedido</h1><p>Formulário com nome, telefone e endereço de entrega.</p>';
})->name('checkout.index');


// 2. ÁREA AUTENTICADA GERAL (Para qualquer usuário logado)
Route::middleware('auth')->group(function () {

    // Perfil do usuário (já gerado pelo Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tela de histórico de pedidos do próprio cliente
    Route::get('/meus-pedidos', function () {
        return '<h1>Meus Pedidos</h1><p>Acompanhamento do status dos seus pedidos.</p>';
    })->name('cliente.pedidos');
});


// 3. ÁREA INTERNA DA EMPRESA (Funcionários e Chefe)
// Exclusivo para quem gerencia a operação (ver pedidos da loja, preparar entrega, etc.)
Route::middleware(['auth', 'role:funcionario,chefe'])->prefix('gerencial')->group(function () {

    Route::get('/pedidos', function () {
        return '<h1>Painel Operacional de Pedidos</h1><p>Lista de todos os pedidos recebidos, cliente, itens e endereço de entrega.</p>';
    })->name('gerencial.pedidos');

});


// 4. ÁREA ESTRATÉGICA (Exclusivo do Chefe)
// Apenas o Chefe tem acesso às métricas financeiras e relatórios
Route::middleware(['auth', 'role:chefe'])->prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return '<h1>Dashboard Financeira do Chefe</h1><p>Métricas de vendas, lucros e total de pedidos.</p>';
    })->name('chefe.dashboard');

});

require __DIR__.'/auth.php';
