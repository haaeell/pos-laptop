<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', [CatalogController::class, 'index']);
Route::get('/data/catalog', [CatalogController::class, 'data'])->name('catalog.data');

Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard/inventory', [HomeController::class, 'inventoryList'])->name('dashboard.inventory');

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */
    Route::prefix('categories')
        ->controller(CategoryController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Brands
    |--------------------------------------------------------------------------
    */
    Route::prefix('brands')
        ->controller(BrandController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')
        ->controller(ProductController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Sales (Transactions)
    |--------------------------------------------------------------------------
    */
    Route::prefix('sales')
        ->controller(SaleController::class)
        ->group(function () {
            Route::get('/', 'index')->name('sales.index');
            Route::get('/create', 'create')->name('sales.create');
            Route::post('/', 'store')->name('sales.store');    // invoice / detail
            Route::get('/{id}/detail', 'detail'); // cetak invoice
            Route::get('/{id}/invoice-pdf', 'invoicePdf')->name('sales.invoice.pdf');
        });

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
    });
});
