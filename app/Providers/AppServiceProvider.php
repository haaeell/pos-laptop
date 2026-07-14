<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Setting;
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
        View::composer(['layouts.catalog', 'welcome', 'catalog.show'], function ($view) {
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
    }
}
