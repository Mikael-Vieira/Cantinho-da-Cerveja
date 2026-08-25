<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// 1. ROTA PRINCIPAL / CARDÁPIO
Route::get('/', [ProductController::class, 'index'])->name('products.index');

// ROTA DE REDIRECIONAMENTO DE LOGIN (Resolve o erro do Breeze)
Route::get('/dashboard', function () {
    return redirect()->route('orders.index');
})->middleware(['auth'])->name('dashboard');

// 2. ROTAS DO CARRINHO DE COMPRAS
Route::get('/carrinho', [CartController::class, 'index'])->name('carrinho.index');
Route::post('/carrinho/adicionar/{product}', [CartController::class, 'add'])->name('carrinho.add');
Route::post('/carrinho/remover/{product}', [CartController::class, 'remove'])->name('carrinho.remove');
Route::post('/carrinho/limpar', [CartController::class, 'clear'])->name('carrinho.clear');

// 3. ROTAS DE CHECKOUT E CRIAÇÃO DE PEDIDO (PÚBLICAS)
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

// 4. PAINEL DOS FUNCIONÁRIOS DA COZINHA (EXIGE LOGIN)
Route::middleware(['auth'])->group(function () {
    Route::get('/pedidos-cozinha', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/pedidos-cozinha/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// 5. ÁREA ESTRATÉGICA (EXCLUSIVO DO CHEFE)
Route::middleware(['auth', 'role:chefe'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return '<h1>Dashboard Financeira do Chefe</h1><p>Métricas de vendas, lucros e total de pedidos.</p>';
    })->name('chefe.dashboard');
});

// ROTAS DE AUTENTICAÇÃO DO BREEZE (Login, Logout, etc.)
require __DIR__.'/auth.php';
