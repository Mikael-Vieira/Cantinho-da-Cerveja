<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeController;

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

    // Criação de Pedido (finalizar via modal no carrinho)
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

// 4. ÁREA ESTRATÉGICA E DE GESTÃO (EXCLUSIVO DO ADMIN / CHEFE)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // Cadastro de Produtos
    Route::get('/produtos/criar', [ProductController::class, 'create'])->name('products.create');
    Route::post('/produtos', [ProductController::class, 'store'])->name('products.store');

    // Cadastro de Categorias
    Route::get('/categorias/criar', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categorias', [CategoryController::class, 'store'])->name('categories.store');

    // Dashboard Financeiro
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('chefe.dashboard');
    Route::get('/dashboard/exportar-csv', [AdminDashboardController::class, 'exportCsv'])->name('chefe.dashboard.export');

    // Ações sobre pedidos no histórico do dashboard
    Route::patch('/pedidos/{order}/cancelar', [AdminDashboardController::class, 'cancelar'])->name('chefe.pedidos.cancelar');
    Route::patch('/pedidos/{order}/reabrir', [AdminDashboardController::class, 'reabrir'])->name('chefe.pedidos.reabrir');
    Route::delete('/pedidos/{order}', [AdminDashboardController::class, 'destroy'])->name('chefe.pedidos.destroy');

    //cadastro de funcionarios
    Route::get('/funcionarios/criar', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/funcionarios', [EmployeeController::class, 'store'])->name('employees.store');
    Route::delete('/funcionarios/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});

// ROTAS DE AUTENTICAÇÃO DO BREEZE (Login, Registro, Logout, etc.)
require __DIR__ . '/auth.php';
