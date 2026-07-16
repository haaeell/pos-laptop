<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PenjualController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\BiteshipWebhookController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index']);
Route::get('/data/catalog', [CatalogController::class, 'data'])->name('catalog.data');
Route::get('/produk', [CatalogController::class, 'listing'])->name('catalog.listing');
Route::get('/produk/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/service', [PageController::class, 'service'])->name('pages.service');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('pages.about');
Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/security', [PageController::class, 'security'])->name('pages.security');
Route::get('/artikel', [PageController::class, 'articles'])->name('pages.articles');
Route::get('/artikel/{slug}', [PageController::class, 'articleShow'])->name('pages.article-show');

Auth::routes();

// ================= CUSTOMER (storefront) AUTH =================
Route::prefix('akun')->name('customer.')->group(function () {
    Route::middleware('guest:customers')->group(function () {
        Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [CustomerAuthController::class, 'register'])->middleware('throttle:6,1');
        Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'login'])->middleware('throttle:6,1');
    });

    Route::middleware('auth:customers')->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
        Route::get('/pesanan', [CustomerOrderController::class, 'index'])->name('orders.index');
        Route::post('/pesanan/ulasan', [CustomerReviewController::class, 'store'])->name('reviews.store');
        Route::get('/pesanan/{orderNumber}', [CustomerOrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{orderNumber}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');

        Route::prefix('alamat')->name('addresses.')->controller(AddressController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/default', 'setDefault')->name('set-default');
        });

        Route::prefix('profil')->name('profile.')->controller(CustomerProfileController::class)->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::put('/', 'update')->name('update');
        });

        Route::get('/favorit', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/produk/{product}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    });
});

// ================= CART & CHECKOUT (customer) =================
Route::middleware('auth:customers')->group(function () {
    Route::prefix('keranjang')->name('cart.')->controller(CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/tambah', 'add')->name('add');
        Route::post('/{id}/update', 'updateQty')->name('update');
        Route::delete('/{id}', 'remove')->name('remove');
    });

    Route::prefix('checkout')->name('checkout.')->controller(CheckoutController::class)->group(function () {
        Route::get('/', 'create')->name('create');
        Route::post('/rates', 'rates')->name('rates');
        Route::post('/referral/validate', 'validateReferral')->name('referral.validate');
        Route::post('/', 'store')->name('store');
        Route::get('/{orderNumber}/pay', 'pay')->name('pay');
    });
});

// ================= MIDTRANS WEBHOOK =================
Route::post('/midtrans/notification', [MidtransNotificationController::class, 'handle'])->name('midtrans.notification');

// ================= BITESHIP WEBHOOK =================
Route::post('/biteship/webhook', [BiteshipWebhookController::class, 'handle'])->name('biteship.webhook');

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
        Route::post('/{id}/pay', 'pay')->name('sales.pay');
        Route::get('/{id}/invoice-pdf', 'invoicePdf')->name('sales.invoice.pdf');
        Route::delete('/{id}', 'destroy')->name('sales.destroy');
    });

    // Pesanan Online
    Route::prefix('orders')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index')->name('orders.index');
        Route::get('/notifications/latest', 'latestNotifications')->name('orders.notifications.latest');
        Route::get('/{id}', 'show')->name('orders.show');
        Route::get('/{id}/invoice-pdf', 'invoicePdf')->name('orders.invoice.pdf');
        Route::post('/{id}/advance', 'advance')->name('orders.advance');
        Route::post('/{id}/cancel', 'cancel')->name('orders.cancel');
        Route::post('/{id}/shipment', 'createShipment')->name('orders.shipment.create');
        Route::post('/{id}/shipment/refresh', 'refreshTracking')->name('orders.shipment.refresh');
        Route::get('/{id}/shipment/label', 'downloadShipmentLabel')->name('orders.shipment.label');
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

    // Produk
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('/export-pdf', 'exportPdf')->name('products.export-pdf');
        Route::post('/upload-description-image', 'uploadDescriptionImage')->name('products.upload-description-image');
        Route::get('/', 'index')->name('products.index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {

    // Kategori
    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::post('/{id}/toggle-visibility', 'toggleVisibility')->name('categories.toggle-visibility');
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
        Route::post('/biteship/search-area', 'searchBiteshipArea')->name('settings.biteship.search-area');
    });

    // Kurir
    Route::prefix('couriers')->controller(CourierController::class)->group(function () {
        Route::get('/', 'index')->name('couriers.index');
        Route::post('/{id}/toggle-active', 'toggleActive')->name('couriers.toggle-active');
        Route::post('/{id}/logo', 'updateLogo')->name('couriers.update-logo');
    });

    // Brand
    Route::prefix('brands')->controller(BrandController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::post('/{id}/toggle-partner', 'togglePartner')->name('brands.toggle-partner');
        Route::delete('/{id}', 'destroy');
    });

    // Produk (import massal & template — berisi harga beli, super admin only)
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('/template', 'template')->name('products.template');
        Route::post('/import', 'import')->name('products.import');
    });

    // Artikel
    Route::prefix('articles')->controller(ArticleController::class)->group(function () {
        Route::post('/upload-content-image', 'uploadContentImage')->name('articles.upload-content-image');
        Route::get('/', 'index')->name('articles.index');
        Route::post('/', 'store')->name('articles.store');
        Route::put('/{id}', 'update')->name('articles.update');
        Route::delete('/{id}', 'destroy')->name('articles.destroy');
    });

    // Kategori Artikel
    Route::prefix('article-categories')->controller(ArticleCategoryController::class)->group(function () {
        Route::get('/', 'index')->name('article-categories.index');
        Route::post('/', 'store')->name('article-categories.store');
        Route::put('/{id}', 'update')->name('article-categories.update');
        Route::delete('/{id}', 'destroy')->name('article-categories.destroy');
    });

    // Ulasan Produk
    Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
        Route::get('/', 'index')->name('reviews.index');
        Route::delete('/{id}', 'destroy')->name('reviews.destroy');
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
