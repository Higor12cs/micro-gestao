<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemsController;
use App\Http\Controllers\OrderReceivablesController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseItemsController;
use App\Http\Controllers\PurchasePayablesController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::view('/home', 'home.index')->name('home.index');
    Route::view('/dashboard', 'dashboard.index')->name('dashboard.index');

    Route::view('/clientes', 'customers.index')->name('customers.index');
    Route::view('/fornecedores', 'suppliers.index')->name('suppliers.index');

    Route::view('/pagaveis', 'payables.index')->name('payables.index');
    Route::view('/recebiveis', 'receivables.index')->name('receivables.index');

    Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');

    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::view('/pedidos/nova', 'orders.create')->name('orders.create');
    Route::post('/pedidos', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/pedidos/{sequential}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/pedidos/{sequential}/editar', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/pedidos/{sequential}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/pedidos/{sequential}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/pedidos/{order}/itens', [OrderItemsController::class, 'index'])->name('orders.items.index');
    Route::post('/pedidos/{order}/itens', [OrderItemsController::class, 'store'])->name('orders.items.store');
    Route::delete('/pedidos/{order}/itens/{item}', [OrderItemsController::class, 'destroy'])->name('orders.items.destroy');
    Route::get('/pedidos/{order:sequential}/recebiveis', [OrderReceivablesController::class, 'index'])->name('orders.receivables.index');
    Route::post('/pedidos/{order:sequential}/recebiveis', [OrderReceivablesController::class, 'store'])->name('orders.receivables.store');

    Route::get('/compras', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::view('/compras/nova', 'purchases.create')->name('purchases.create');
    Route::post('/compras', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/compras/{sequential}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/compras/{sequential}/editar', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/compras/{sequential}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/compras/{sequential}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    Route::get('/compras/{purchase}/itens', [PurchaseItemsController::class, 'index'])->name('purchases.items.index');
    Route::post('/compras/{purchase}/itens', [PurchaseItemsController::class, 'store'])->name('purchases.items.store');
    Route::delete('/compras/{purchase}/itens/{item}', [PurchaseItemsController::class, 'destroy'])->name('purchases.items.destroy');
    Route::get('/compras/{purchase:sequential}/pagaveis', [PurchasePayablesController::class, 'index'])->name('purchases.payables.index');
    Route::post('/compras/{purchase:sequential}/pagaveis', [PurchasePayablesController::class, 'store'])->name('purchases.payables.store');

    Route::view('/secoes', 'sections.index')->name('sections.index');
    Route::view('/grupos', 'groups.index')->name('groups.index');
    Route::view('/marcas', 'brands.index')->name('brands.index');

    Route::view('/usuarios', 'users.index')->name('users.index');

    Route::prefix('/ajax')->as('ajax.')->group(function () {
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/suppliers/search', [SupplierController::class, 'search'])->name('suppliers.search');
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/sections/search', [SectionController::class, 'search'])->name('sections.search');
        Route::get('/groups/search', [GroupController::class, 'search'])->name('groups.search');
        Route::get('/brands/search', [BrandController::class, 'search'])->name('brands.search');
        Route::get('/users/search', [UserController::class, 'search'])->name('users.search');

        Route::apiResource('/customers', CustomerController::class);
        Route::apiResource('/suppliers', SupplierController::class);
        Route::apiResource('/payables', PayableController::class);
        Route::apiResource('/receivables', ReceivableController::class);
        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/orders', OrderController::class);
        Route::apiResource('/purchases', PurchaseController::class);
        Route::apiResource('/sections', SectionController::class);
        Route::apiResource('/groups', GroupController::class);
        Route::apiResource('/brands', BrandController::class);
        Route::apiResource('/users', UserController::class);
    });
});

Route::redirect('/', '/login');
