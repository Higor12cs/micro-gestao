<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
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

    Route::view('/contas-a-pagar', 'payables.index')->name('payables.index');

    Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');

    Route::view('/compras', 'purchases.index')->name('purchases.index');
    Route::view('/compras/nova', 'purchases.create')->name('purchases.create');
    Route::post('/compras', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/compras/{purchase:sequential}/visualizar', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/compras/{purchase:sequential}/editar', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::get('/compras/{purchase:sequential}/pagaveis', [PurchaseController::class, 'purchasePayables'])->name('purchases.payables');
    Route::post('/compras/{purchase:sequential}/pagaveis', [PurchaseController::class, 'storePayables'])->name('purchases.store-payables');

    Route::get('/purchases/{purchase}/items', [PurchaseController::class, 'items']);
    Route::post('/purchases/{purchase}/items', [PurchaseController::class, 'addItem']);
    Route::delete('/purchases/{purchase}/items/{item}', [PurchaseController::class, 'deleteItem']);

    Route::view('/secoes', 'sections.index')->name('sections.index');
    Route::view('/grupos', 'groups.index')->name('groups.index');
    Route::view('/marcas', 'brands.index')->name('brands.index');

    Route::view('/usuarios', 'users.index')->name('users.index');

    // Ajax
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
        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/purchases', PurchaseController::class);
        Route::apiResource('/sections', SectionController::class);
        Route::apiResource('/groups', GroupController::class);
        Route::apiResource('/brands', BrandController::class);
        Route::apiResource('/users', UserController::class);
    });
});

Route::redirect('/', '/login');
