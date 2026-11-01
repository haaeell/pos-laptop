<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\BookPeriod;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Modal;
use App\Models\ModalCicilan;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Sale;
use App\Models\SaleBonus;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Service;
use App\Models\ServiceTechnician;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([
            Sale::class => 'created_at', SalePayment::class => 'paid_at',
            Order::class => 'paid_at', Rental::class => 'created_at',
            Service::class => 'done_at', Expense::class => 'entry_date',
            Payroll::class => 'release_date', Modal::class => 'tanggal_pencairan',
            ModalCicilan::class => 'tanggal_bayar',
        ] as $model => $dateColumn) {
            $guard = function (Model $record) use ($dateColumn): void {
                if (!\Illuminate\Support\Facades\Schema::hasTable('book_periods')) return;
                $date = $record->exists ? $record->getRawOriginal($dateColumn) : $record->{$dateColumn};
                if (!$date && $record->exists && ($record instanceof Service || $record instanceof Order)) {
                    $date = $record->getRawOriginal('created_at');
                }
                if ($record->exists && $record->isDirty($dateColumn) && $record->{$dateColumn} && BookPeriod::closedOn($record->{$dateColumn})) {
                    abort(422, 'Tanggal buku tertutup tidak dapat digunakan.');
                }
                if (!$record->exists && !BookPeriod::active() && BookPeriod::exists()) {
                    abort(422, 'Buka buku baru sebelum mencatat transaksi.');
                }
                if (!$record->exists && $date && BookPeriod::active() && \Carbon\Carbon::parse($date)->startOfDay()->lt(BookPeriod::active()->starts_at)) {
                    abort(422, 'Tanggal transaksi sebelum awal buku aktif.');
                }
                if ($date && BookPeriod::closedOn($date)) {
                    if ($record instanceof Sale && $record->exists && $record->isDirty() &&
                        !array_diff(array_keys($record->getDirty()), ['paid_amount', 'payment_status', 'due_date', 'updated_at'])) {
                        return; // Pembayaran piutang lama dicatat pada buku baru.
                    }
                    abort(422, 'Transaksi dalam buku tertutup tidak dapat diubah.');
                }
            };
            $model::saving($guard);
            $model::deleting($guard);
        }

        foreach ([
            SaleItem::class => [Sale::class, 'sale_id'], SaleBonus::class => [Sale::class, 'sale_id'],
            RentalItem::class => [Rental::class, 'rental_id'], ServiceTechnician::class => [Service::class, 'service_id'],
            OrderItem::class => [Order::class, 'order_id'], OrderStatusHistory::class => [Order::class, 'order_id'],
            PayrollDetail::class => [Payroll::class, 'payroll_id'],
        ] as $model => [$parent, $foreignKey]) {
            $guard = function (Model $record) use ($parent, $foreignKey): void {
                if (!\Illuminate\Support\Facades\Schema::hasTable('book_periods')) return;
                $item = $parent::find($record->{$foreignKey});
                $date = $item?->getRawOriginal(match ($parent) {
                    Order::class => 'paid_at', Service::class => 'done_at', Payroll::class => 'release_date',
                    default => 'created_at',
                }) ?: $item?->getRawOriginal('created_at');
                if ($date && BookPeriod::closedOn($date)) abort(422, 'Rincian transaksi buku tertutup tidak dapat diubah.');
            };
            $model::saving($guard);
            $model::deleting($guard);
        }

        View::composer([
            'layouts.catalog',
            'welcome',
            'catalog.show',
            'customer.auth.login',
            'customer.auth.register',
            'customer.orders.index',
            'customer.orders.show',
            'customer.addresses.index',
            'customer.profile.edit',
            'customer.favorites.index',
            'produk.index',
            'pages.service',
            'pages.about',
            'pages.privacy',
            'pages.security',
            'pages.articles',
            'pages.article-show',
            'cart.index',
            'checkout.create',
            'checkout.pay',
        ], function ($view) {
            $settings = Setting::pluck('value', 'key');

            $view->with([
                'navCategories' => Category::orderBy('name')->get(),
                'navContacts'   => Contact::where('is_active', true)->get(),
                'navSettings'   => $settings,
                'namaToko'      => $settings['nama_toko'] ?? 'Barokah Computer',
                'alamat'        => $settings['alamat'] ?? 'Alamat toko belum diatur',
                'jamBuka'       => $settings['jam_buka'] ?? '09.00 – 21.00',
                'deskripsi'     => $settings['deskripsi'] ?? 'Laptop & elektronik berkualitas dengan harga terbaik.',
                'logo'          => $settings['logo'] ?? 'logo.jpeg',
            ]);
        });

        View::composer('layouts.app', function ($view) {
            if (!auth()->check()) {
                return;
            }

            $newOrders = Order::with('customer')
                ->where('status', 'paid')
                ->latest()
                ->take(5)
                ->get();

            $view->with([
                'newOrdersCount' => Order::where('status', 'paid')->count(),
                'newOrders'      => $newOrders,
            ]);
        });
    }
}
