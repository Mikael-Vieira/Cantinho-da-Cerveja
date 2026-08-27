<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// 1. ROTA PRINCIPAL / CARDÁPIO (Pública para navegação)
Route::get('/', [ProductController::class, 'index'])->name('products.index');

// ROTA DE REDIRECIONAMENTO DE LOGIN (Resolve o erro do Breeze)
Route::get('/dashboard', function () {
    return redirect()->route('products.index');
})->middleware(['auth'])->name('dashboard');

// 2. ROTAS EXCLUSIVAS PARA USUÁRIOS LOGADOS (Clientes e Funcionários)
Route::middleware(['auth'])->group(function () {

    // Carrinho de Compras
    Route::get('/carrinho', [CartController::class, 'index'])->name('carrinho.index');
    Route::post('/carrinho/adicionar/{product}', [CartController::class, 'add'])->name('carrinho.add');
    Route::post('/carrinho/remover/{product}', [CartController::class, 'remove'])->name('carrinho.remove');
    Route::post('/carrinho/limpar', [CartController::class, 'clear'])->name('carrinho.clear');

    // Checkout e Criação de Pedido
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

    // Gerenciamento de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 3. PAINEL DA COZINHA (EXCLUSIVO PARA FUNCIONÁRIOS E ADMINS)
Route::middleware(['auth', 'role:admin,funcionario'])->group(function () {
    Route::get('/pedidos-cozinha', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/pedidos-cozinha/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// 4. ÁREA ESTRATÉGICA (EXCLUSIVO DO ADMIN / CHEFE)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return '<h1>Dashboard Financeira do Chefe</h1><p>Métricas de vendas, lucros e total de pedidos.</p>';
    })->name('chefe.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/carrinho/finalizar', [CartController::class, 'checkout'])->name('cart.checkout');
});

// ROTAS DE AUTENTICAÇÃO DO BREEZE (Login, Registro, Logout, etc.)
require __DIR__.'/auth.php';
