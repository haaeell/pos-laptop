<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PenjualController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
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
        }); /*
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
    | Contacts
    |--------------------------------------------------------------------------
    */
    Route::prefix('contacts')
        ->controller(ContactController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    Route::prefix('penjuals')
        ->controller(PenjualController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    Route::prefix('expenses')
        ->controller(ExpenseController::class)
        ->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')
        ->controller(SettingController::class)
        ->group(function () {
            Route::get('/', 'index')->name('settings.index');
            Route::post('/', 'update')->name('settings.update');
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
            Route::get('/template', 'template')->name('products.template');
            Route::post('/import', 'import')->name('products.import');
            Route::get('/', 'index')->name('products.index');
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
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
        Route::get('/excel', [ReportController::class, 'excel'])->name('reports.excel');
    });

    Route::prefix('profile')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
    });
});
