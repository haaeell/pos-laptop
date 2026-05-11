<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PenjualController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index']);
Route::get('/data/catalog', [CatalogController::class, 'data'])->name('catalog.data');

Auth::routes();

Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {

    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard/inventory', [HomeController::class, 'inventoryList'])->name('dashboard.inventory');

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Penjualan / Transaksi
    Route::prefix('sales')->controller(SaleController::class)->group(function () {
        Route::get('/', 'index')->name('sales.index');
        Route::get('/create', 'create')->name('sales.create');
        Route::post('/', 'store')->name('sales.store');
        Route::get('/{id}/detail', 'detail');
        Route::get('/{id}/invoice-pdf', 'invoicePdf')->name('sales.invoice.pdf');
        Route::delete('/{id}', 'destroy')->name('sales.destroy');
    });

    // Laporan
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
        Route::get('/excel', [ReportController::class, 'excel'])->name('reports.excel');
    });

    Route::prefix('services')->controller(ServiceController::class)->group(function () {
        Route::get('/', 'index')->name('services.index');
        Route::post('/', 'store')->name('services.store');
        Route::post('/{id}/estimate', 'estimate')->name('services.estimate');
        Route::post('/{id}/confirm', 'confirm')->name('services.confirm');
        Route::post('/{id}/done', 'done')->name('services.done');
        Route::post('/{id}/taken', 'taken')->name('services.taken');
        Route::get('/{id}/print-receive', 'printReceive')->name('services.print-receive');
        Route::get('/{id}/print-pickup', 'printPickup')->name('services.print-pickup');
        Route::delete('/{id}', 'destroy')->name('services.destroy');
    });
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {

    // Kategori
    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Kontak / WhatsApp
    Route::prefix('contacts')->controller(ContactController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Sales Person (Penjual)
    Route::prefix('penjuals')->controller(PenjualController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('/{id}/promote', 'promoteToEmployee')->name('penjuals.promote');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Pengeluaran
    Route::prefix('expenses')->controller(ExpenseController::class)->group(function () {
        Route::get('/export-pdf', 'exportPdf')->name('expenses.export-pdf');
        Route::get('/', 'index')->name('expenses.index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Setting Toko
    Route::prefix('settings')->controller(SettingController::class)->group(function () {
        Route::get('/', 'index')->name('settings.index');
        Route::post('/', 'update')->name('settings.update');
    });

    // Brand
    Route::prefix('brands')->controller(BrandController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Produk
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('/template', 'template')->name('products.template');
        Route::post('/import', 'import')->name('products.import');
        Route::get('/', 'index')->name('products.index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    // Modal / Hutang
    Route::prefix('modals')->controller(ModalController::class)->group(function () {
        Route::get('/', 'index')->name('modals.index');
        Route::post('/', 'store')->name('modals.store');
        Route::get('/{id}', 'show')->name('modals.show');
        Route::put('/{id}', 'update')->name('modals.update');
        Route::delete('/{id}', 'destroy')->name('modals.destroy');
        Route::post('/{id}/bayar-cicilan', 'bayarCicilan')->name('modals.bayar-cicilan');
        Route::post('/kalkulasi', 'kalkulasi')->name('modals.kalkulasi');
    });

    // Karyawan
    Route::prefix('employees')->controller(EmployeeController::class)->group(function () {
        Route::get('/', 'index')->name('employees.index');
        Route::post('/', 'store')->name('employees.store');
        Route::put('/{id}', 'update')->name('employees.update');
        Route::delete('/{id}', 'destroy')->name('employees.destroy');
    });

    // Penggajian
    Route::prefix('payrolls')->controller(PayrollController::class)->group(function () {
        Route::get('/', 'index')->name('payrolls.index');
        Route::get('/create', 'create')->name('payrolls.create');
        Route::post('/calculate', 'calculate')->name('payrolls.calculate');
        Route::post('/', 'store')->name('payrolls.store');
        Route::post('/{id}/release', 'release')->name('payrolls.release');
        Route::get('/{payrollId}/slip/{employeeId}', 'printSlip')->name('payrolls.slip');
        Route::delete('/{id}', 'destroy')->name('payrolls.destroy');
    });

    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('users.index');
        Route::post('/', 'store')->name('users.store');
        Route::put('/{id}', 'update')->name('users.update');
        Route::delete('/{id}', 'destroy')->name('users.destroy');
    });
});
