<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\OrderReceivableController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\PurchasePayableController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\Report\OrderReportController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::view('/home', 'home.index')->name('home.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::view('/clientes', 'customers.index')->name('customers.index');
    Route::view('/fornecedores', 'suppliers.index')->name('suppliers.index');

    Route::view('/pagaveis', 'payables.index')->name('payables.index');
    Route::view('/recebiveis', 'receivables.index')->name('receivables.index');

    Route::view('/contas', 'accounts.index')->name('accounts.index');

    Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');

    Route::get('/kardex/{product:sequential?}', [KardexController::class, 'index'])->name('kardex.index');
    Route::post('/kardex', [KardexController::class, 'redirectToProduct'])->name('kardex.redirect');
    Route::get('/kardex/movements/{product:sequential}', [KardexController::class, 'getMovements'])->name('kardex.movements');

    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::view('/pedidos/novo', 'orders.create')->name('orders.create');
    Route::post('/pedidos', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/pedidos/{sequential}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/pedidos/{sequential}/editar', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/pedidos/{sequential}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/pedidos/{sequential}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/pedidos/{order}/itens', [OrderItemController::class, 'index'])->name('orders.items.index');
    Route::post('/pedidos/{order}/itens', [OrderItemController::class, 'store'])->name('orders.items.store');
    Route::delete('/pedidos/{order}/itens/{orderItem}', [OrderItemController::class, 'destroy'])->name('orders.items.destroy');
    Route::get('/pedidos/{order:sequential}/recebiveis', [OrderReceivableController::class, 'index'])->name('orders.receivables.index');
    Route::post('/pedidos/{order:sequential}/recebiveis', [OrderReceivableController::class, 'store'])->name('orders.receivables.store');

    Route::get('/compras', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::view('/compras/nova', 'purchases.create')->name('purchases.create');
    Route::post('/compras', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/compras/{sequential}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/compras/{sequential}/editar', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/compras/{sequential}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/compras/{sequential}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    Route::get('/compras/{purchase}/itens', [PurchaseItemController::class, 'index'])->name('purchases.items.index');
    Route::post('/compras/{purchase}/itens', [PurchaseItemController::class, 'store'])->name('purchases.items.store');
    Route::delete('/compras/{purchase}/itens/{purchaseItem}', [PurchaseItemController::class, 'destroy'])->name('purchases.items.destroy');
    Route::get('/compras/{purchase:sequential}/pagaveis', [PurchasePayableController::class, 'index'])->name('purchases.payables.index');
    Route::post('/compras/{purchase:sequential}/pagaveis', [PurchasePayableController::class, 'store'])->name('purchases.payables.store');

    Route::view('/secoes', 'sections.index')->name('sections.index');
    Route::view('/grupos', 'groups.index')->name('groups.index');
    Route::view('/marcas', 'brands.index')->name('brands.index');

    Route::prefix('/relatorios')->as('reports.')->group(function () {
        Route::view('/pedidos', 'reports.orders.index')->name('orders.index');
        Route::post('/pedidos/relatorio', [OrderReportController::class, 'report'])->name('orders.report');
    });

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
        Route::apiResource('/accounts', AccountController::class);
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
