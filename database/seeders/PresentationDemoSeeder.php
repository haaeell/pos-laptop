<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalRateTier;
use App\Models\Sale;
use App\Models\SalesPerson;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PresentationDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $admin = User::updateOrCreate(['email' => 'demo.admin@barokah.test'], ['name' => 'Admin Demo', 'password' => Hash::make('password'), 'role' => 'super_admin']);
            $categories = collect(['Laptop', 'Aksesoris', 'Komponen'])->mapWithKeys(fn ($name) => [$name => Category::updateOrCreate(['slug' => 'demo-' . Str::slug($name)], ['name' => $name])]);
            $brands = collect(['ASUS', 'Lenovo', 'Logitech', 'Samsung'])->mapWithKeys(fn ($name) => [$name => Brand::updateOrCreate(['slug' => 'demo-' . Str::slug($name)], ['name' => $name])]);
            $products = collect([
                ['DEMO-LAP-001', 'ASUS Vivobook 14 Demo', 'Laptop', 'ASUS', 6500000, 7499000, 4],
                ['DEMO-LAP-002', 'Lenovo IdeaPad Slim 3 Demo', 'Laptop', 'Lenovo', 5800000, 6799000, 3],
                ['DEMO-ACC-001', 'Logitech M331 Silent Demo', 'Aksesoris', 'Logitech', 180000, 249000, 12],
                ['DEMO-CMP-001', 'Samsung SSD 1TB Demo', 'Komponen', 'Samsung', 850000, 1099000, 8],
            ])->mapWithKeys(function ($row) use ($categories, $brands) {
                [$code, $name, $category, $brand, $buy, $sell, $stock] = $row;
                $product = Product::updateOrCreate(['product_code' => $code], [
                    'name' => $name, 'category_id' => $categories[$category]->id, 'brand_id' => $brands[$brand]->id,
                    'condition' => 'new', 'purchase_price' => $buy, 'selling_price' => $sell, 'strike_price' => $sell + 200000,
                    'status' => 'available', 'is_active' => true, 'stock' => $stock, 'weight' => 1000,
                    'description' => 'Unit demo untuk presentasi katalog.', 'notes' => 'Stok demo.',
                ]);
                return [$code => $product];
            });

            $sales = SalesPerson::updateOrCreate(['phone' => '081200000101'], ['name' => 'Raka Demo', 'phone' => '081200000101', 'active' => true, 'fee' => 50000, 'referral_code' => 'DEMO-RAKA']);
            $employee = Employee::updateOrCreate(['employee_number' => 'DEMO-EMP-001'], ['full_name' => 'Dimas Teknisi', 'position' => 'Teknisi', 'join_date' => now()->subYear(), 'birth_date' => '1995-06-12', 'phone' => '081200000102', 'address' => 'Subang, Jawa Barat', 'basic_salary' => 3500000, 'is_active' => true]);
            $sales->update(['employee_id' => $employee->id]);

            $customers = collect([
                ['Nadia Prameswari', 'demo.nadia@barokah.test', '081200000201'],
                ['Fajar Ramadhan', 'demo.fajar@barokah.test', '081200000202'],
            ])->mapWithKeys(function ($row) {
                [$name, $email, $phone] = $row;
                $customer = Customer::updateOrCreate(['email' => $email], compact('name', 'email', 'phone') + ['password' => Hash::make('password'), 'is_active' => true]);
                CustomerAddress::updateOrCreate(['customer_id' => $customer->id, 'label' => 'Rumah Demo'], ['recipient_name' => $name, 'recipient_phone' => $phone, 'province' => 'Jawa Barat', 'city' => 'Subang', 'district' => 'Subang', 'postal_code' => '41211', 'area_id' => 'IDSBG', 'address_detail' => 'Jl. Demo Presentasi No. 10', 'is_default' => true]);
                return [$email => $customer];
            });

            $this->sale($admin, $sales, $products['DEMO-LAP-001']);
            $this->order($customers['demo.nadia@barokah.test'], $sales, $products['DEMO-LAP-002'], 'DEMO-ORD-001', 'completed');
            $this->order($customers['demo.fajar@barokah.test'], $sales, $products['DEMO-CMP-001'], 'DEMO-ORD-002', 'shipped');
            $customers['demo.nadia@barokah.test']->favoriteProducts()->syncWithoutDetaching([$products['DEMO-LAP-001']->id]);
            DB::table('cart_items')->updateOrInsert(['customer_id' => $customers['demo.fajar@barokah.test']->id, 'product_id' => $products['DEMO-ACC-001']->id], ['qty' => 1, 'created_at' => now(), 'updated_at' => now()]);

            Service::updateOrCreate(['service_number' => 'DEMO-SVC-001'], ['customer_name' => 'Rizky Maulana', 'customer_phone' => '081200000301', 'device_type' => 'Laptop', 'device_brand' => 'ASUS', 'device_sn' => 'DEMO-SN-001', 'complaint' => 'Laptop lambat dan cepat panas.', 'technician_notes' => 'Cleaning fan dan upgrade SSD.', 'spare_parts' => [['name' => 'SSD 512GB', 'qty' => 1, 'price' => 650000]], 'spare_part_cost' => 650000, 'spare_part_hpp' => 500000, 'service_cost' => 150000, 'total_cost' => 800000, 'status' => 'done', 'estimated_done' => now()->addDay()->toDateString(), 'done_at' => now()->subHour(), 'created_by' => $admin->id]);
            RentalRateTier::updateOrCreate(['min_qty' => 1], ['daily_rate' => 150000]);
            $rental = Rental::updateOrCreate(['rental_number' => 'DEMO-RENT-001'], ['user_id' => $admin->id, 'renter_name' => 'CV Kreatif Nusantara', 'address' => 'Jl. Otto Iskandardinata No. 88, Subang', 'purpose' => 'Pelatihan kantor', 'person_in_charge' => 'Sinta Wulandari', 'phone' => '081200000401', 'rental_dates' => [now()->toDateString(), now()->addDays(2)->toDateString()], 'planned_return_date' => now()->addDays(2)->toDateString(), 'total_qty' => 1, 'daily_rate' => 150000, 'rental_total' => 450000, 'fine_amount' => 0, 'status' => 'active']);
            DB::table('rental_items')->updateOrInsert(['rental_id' => $rental->id, 'product_id' => $products['DEMO-LAP-002']->id], ['product_name' => $products['DEMO-LAP-002']->name, 'product_code' => $products['DEMO-LAP-002']->product_code, 'qty' => 1, 'accessories' => 'Laptop dan charger', 'condition_out' => 'Normal', 'created_at' => now(), 'updated_at' => now()]);
            $payroll = DB::table('payrolls')->updateOrInsert(['payroll_number' => 'DEMO-PAY-' . now()->format('Ym')], ['period_year' => now()->year, 'period_month' => now()->month, 'date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->endOfMonth()->toDateString(), 'release_date' => now()->toDateString(), 'total_amount' => 3550000, 'total_basic_salary' => 3500000, 'total_sales_bonus' => 0, 'total_sales_profit_share' => 0, 'total_rental_share' => 0, 'total_technician_fee' => 50000, 'sales_share_mode' => 'nominal', 'sales_share_value' => 0, 'rental_share_mode' => 'nominal', 'rental_share_value' => 0, 'status' => 'released', 'notes' => 'Payroll data presentasi.', 'released_by' => $admin->id, 'created_at' => now(), 'updated_at' => now()]);
            $payrollId = DB::table('payrolls')->where('payroll_number', 'DEMO-PAY-' . now()->format('Ym'))->value('id');
            DB::table('payroll_details')->updateOrInsert(['payroll_id' => $payrollId, 'employee_id' => $employee->id], ['basic_salary' => 3500000, 'sales_bonus' => 0, 'sales_profit_share' => 0, 'rental_share' => 0, 'technician_fee' => 50000, 'other_allowance' => 0, 'deduction' => 0, 'net_salary' => 3550000, 'total_transactions' => 1, 'sales_transaction_count' => 0, 'rental_transaction_count' => 0, 'created_at' => now(), 'updated_at' => now()]);
            $modalId = DB::table('modals')->updateOrInsert(['kode_pinjaman' => 'DEMO-MDL-001'], ['nama_pemberi_pinjaman' => 'Koperasi Demo Sejahtera', 'jenis_pinjaman' => 'koperasi', 'jumlah_pinjaman' => 12000000, 'nominal_pencairan' => 12000000, 'tenor' => 12, 'satuan_tenor' => 'bulan', 'total_bunga' => 1200000, 'total_kewajiban' => 13200000, 'cicilan_per_periode' => 1100000, 'tanggal_pinjaman' => now()->subMonths(3)->toDateString(), 'tanggal_pencairan' => now()->subMonths(3)->toDateString(), 'tanggal_jatuh_tempo' => now()->addMonths(9)->toDateString(), 'cicilan_ke' => 3, 'total_terbayar' => 3300000, 'sisa_kewajiban' => 9900000, 'status' => 'aktif', 'keterangan' => 'Modal kerja data presentasi.', 'sudah_dicairkan' => true, 'created_at' => now(), 'updated_at' => now()]);
            $modalId = DB::table('modals')->where('kode_pinjaman', 'DEMO-MDL-001')->value('id');
            DB::table('modal_cicilans')->updateOrInsert(['modal_id' => $modalId, 'cicilan_ke' => 1], ['tanggal_jatuh_tempo' => now()->subMonths(2)->toDateString(), 'tanggal_bayar' => now()->subMonths(2)->toDateString(), 'nominal_cicilan' => 1100000, 'denda' => 0, 'total_bayar' => 1100000, 'status' => 'sudah_bayar', 'keterangan' => 'Cicilan demo lunas.', 'created_at' => now(), 'updated_at' => now()]);
            $articleCategoryId = DB::table('article_categories')->updateOrInsert(['slug' => 'demo-tips'], ['name' => 'Tips Demo', 'created_at' => now(), 'updated_at' => now()]);
            $articleCategoryId = DB::table('article_categories')->where('slug', 'demo-tips')->value('id');
            DB::table('articles')->updateOrInsert(['slug' => 'demo-panduan-memilih-laptop'], ['article_category_id' => $articleCategoryId, 'user_id' => $admin->id, 'title' => 'Panduan Memilih Laptop untuk Produktivitas', 'excerpt' => 'Tips singkat memilih laptop sesuai kebutuhan kerja dan belajar.', 'content' => '<p>Pilih laptop berdasarkan kebutuhan, mobilitas, dan anggaran.</p>', 'status' => 'published', 'published_at' => now()->subDays(3), 'views' => 128, 'created_at' => now(), 'updated_at' => now()]);
            Expense::updateOrCreate(['title' => 'DEMO - Internet Operasional', 'entry_date' => now()->toDateString()], ['amount' => 450000, 'category' => 'Operasional', 'description' => 'Data demonstrasi biaya bulanan.', 'user_id' => $admin->id]);
            Setting::updateOrCreate(['key' => 'store_name'], ['value' => 'Barokah Computer']);
            Contact::updateOrCreate(['label' => 'WhatsApp Demo'], ['phone' => '6281200000501', 'whatsapp_text' => 'Halo, saya ingin konsultasi produk.', 'is_active' => true]);
        });
    }

    private function sale(User $admin, SalesPerson $sales, Product $product): void
    {
        $sale = Sale::updateOrCreate(['invoice_number' => 'DEMO-SALE-001'], ['user_id' => $admin->id, 'customer_name' => 'Bapak Arif Demo', 'customer_phone' => '081200000601', 'sales_person_id' => $sales->id, 'grand_total' => $product->selling_price, 'benefit' => $product->selling_price - $product->purchase_price, 'payment_method' => 'transfer', 'payment_status' => 'paid', 'paid_amount' => $product->selling_price, 'fee_sales' => 50000]);
        DB::table('sale_items')->updateOrInsert(['sale_id' => $sale->id, 'product_id' => $product->id], ['purchase_price' => $product->purchase_price, 'selling_price' => $product->selling_price, 'final_price' => $product->selling_price, 'benefit' => $product->selling_price - $product->purchase_price, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('sale_payments')->updateOrInsert(['sale_id' => $sale->id, 'amount' => $product->selling_price], ['user_id' => $admin->id, 'paid_at' => now()->subDays(2)->toDateString(), 'note' => 'Pelunasan demo', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function order(Customer $customer, SalesPerson $sales, Product $product, string $number, string $status): void
    {
        $shipping = $status === 'shipped' ? 25000 : 0;
        $order = Order::updateOrCreate(['order_number' => $number], ['customer_id' => $customer->id, 'sales_person_id' => $sales->id, 'delivery_method' => $shipping ? 'shipping' : 'pickup', 'status' => $status, 'items_subtotal' => $product->selling_price, 'shipping_cost' => $shipping, 'grand_total' => $product->selling_price + $shipping, 'recipient_name' => $customer->name, 'recipient_phone' => $customer->phone, 'province' => 'Jawa Barat', 'city' => 'Subang', 'district' => 'Subang', 'address_detail' => 'Jl. Demo Presentasi No. 10', 'courier_company' => $shipping ? 'jne' : null, 'courier_service_name' => $shipping ? 'REG' : null, 'courier_waybill_id' => $shipping ? 'DEMO-JNE-001' : null, 'shipment_status' => $shipping ? 'in_transit' : null, 'paid_at' => now()->subDays(2), 'shipped_at' => $shipping ? now()->subDay() : null, 'completed_at' => $status === 'completed' ? now()->subDay() : null]);
        DB::table('order_items')->updateOrInsert(['order_id' => $order->id, 'product_id' => $product->id], ['product_name' => $product->name, 'price' => $product->selling_price, 'purchase_price' => $product->purchase_price, 'qty' => 1, 'subtotal' => $product->selling_price, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('order_status_histories')->updateOrInsert(['order_id' => $order->id, 'status' => $status], ['note' => 'Status data presentasi.', 'created_at' => now()]);
    }
}
