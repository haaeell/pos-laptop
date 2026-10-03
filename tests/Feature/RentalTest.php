<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalRateTier;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Tests\TestCase;

class RentalTest extends TestCase
{
    private Product $laptop;

    protected function setUp(): void
    {
        parent::setUp();

        // Only the schema needed by rentals; never use the development database.
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        foreach ([
            '0001_01_01_000000_create_users_table.php',
            '2026_01_25_055040_create_simple_sales_system_tables.php',
            '2026_01_26_060644_create_contacts_table.php',
            '2026_01_26_061911_create_settings_table.php',
            '2026_02_01_145545_add_stock_to_products_table.php',
            '2026_05_10_172120_add_role_to_users_table.php',
            '2026_07_16_120000_add_slug_to_products_table.php',
            '2026_10_03_000001_create_rentals_tables.php',
        ] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $category = Category::create(['name' => 'Laptop', 'slug' => 'laptop']);
        $this->laptop = Product::create([
            'name' => 'ThinkPad T14', 'product_code' => 'LP-001', 'category_id' => $category->id,
            'purchase_price' => 5000000, 'selling_price' => 6000000, 'stock' => 10, 'status' => 'available',
        ]);
        RentalRateTier::create(['min_qty' => 1, 'daily_rate' => 100000]);
        RentalRateTier::create(['min_qty' => 3, 'daily_rate' => 80000]);
    }

    private function payload(): array
    {
        return [
            'renter_name' => 'PT Contoh', 'address' => 'Jl. Contoh No. 10', 'purpose' => 'Pelatihan',
            'person_in_charge' => 'Budi', 'phone' => '081234567890',
            'rental_dates' => ['2026-10-08', '2026-10-05'], 'planned_return_date' => '2026-10-08',
            'items' => [['product_id' => $this->laptop->id, 'qty' => 3, 'accessories' => 'Laptop, charger, tas', 'condition_out' => 'Normal, gores halus pada casing']],
        ];
    }

    public function test_rental_uses_selected_days_and_quantity_tier_and_saves_handover_details(): void
    {
        $this->post(route('rentals.store'), $this->payload())->assertRedirect(route('rentals.index'))->assertSessionHasNoErrors();
        $rental = Rental::firstOrFail();
        $this->assertSame(['2026-10-05', '2026-10-08'], $rental->rental_dates);
        $this->assertEquals(80000, $rental->daily_rate);
        $this->assertEquals(480000, $rental->rental_total);
        $this->assertSame(7, $this->laptop->fresh()->stock);
        $this->assertSame('Laptop, charger, tas', $rental->items->first()->accessories);
        $this->assertSame('Normal, gores halus pada casing', $rental->items->first()->condition_out);
    }

    public function test_blank_or_duplicate_days_cannot_inflate_the_rental_price(): void
    {
        foreach ([['2026-10-05', ''], ['2026-10-05', '2026-10-05']] as $dates) {
            $data = $this->payload();
            $data['rental_dates'] = $dates;
            $this->post(route('rentals.store'), $data)->assertSessionHasErrors('rental_dates.1');
        }
        $this->assertDatabaseCount('rentals', 0);
        $this->assertSame(10, $this->laptop->fresh()->stock);
    }

    public function test_invoice_preserves_fractional_rates_and_the_original_price_after_rate_changes(): void
    {
        RentalRateTier::where('min_qty', 3)->update(['daily_rate' => 80000.5]);
        $this->post(route('rentals.store'), $this->payload())->assertSessionHasNoErrors();
        RentalRateTier::where('min_qty', 3)->update(['daily_rate' => 90000]);
        $rental = Rental::with('items', 'user')->firstOrFail();
        $this->assertEquals(480003, $rental->rental_total);
        $html = view('rentals.invoice-pdf', ['rental' => $rental, 'settings' => []])->render();
        $this->assertStringContainsString('Rp 80.000,50', $html);
        $this->assertStringContainsString('Rp 480.003', $html);
    }

    public function test_return_date_cannot_precede_the_last_selected_day(): void
    {
        $data = $this->payload();
        $data['planned_return_date'] = '2026-10-06';
        $this->post(route('rentals.store'), $data)->assertSessionHasErrors('planned_return_date');
        $this->assertDatabaseCount('rentals', 0);
    }

    public function test_insufficient_stock_returns_validation_errors_and_keeps_input(): void
    {
        $data = $this->payload();
        $data['items'][0]['qty'] = 11;
        $this->from(route('rentals.create'))->post(route('rentals.store'), $data)
            ->assertRedirect(route('rentals.create'))->assertSessionHasErrors('items')->assertSessionHasInput('renter_name', 'PT Contoh');
        $this->assertDatabaseCount('rentals', 0);
        $this->assertSame(10, $this->laptop->fresh()->stock);
    }

    public function test_duplicate_laptop_rows_are_rejected_to_preserve_clear_handover_details(): void
    {
        $data = $this->payload();
        $data['items'][] = $data['items'][0];
        $this->post(route('rentals.store'), $data)->assertSessionHasErrors('items.1.product_id');
        $this->assertDatabaseCount('rentals', 0);
    }

    public function test_invoice_includes_cost_breakdown_rules_and_return_findings_and_generates_pdf(): void
    {
        $this->post(route('rentals.store'), $this->payload())->assertSessionHasNoErrors();
        $rental = Rental::firstOrFail();
        $item = $rental->items->first();
        $this->post(route('rentals.return', $rental), [
            'fine_amount' => 25000, 'return_notes' => 'Denda keterlambatan sesuai konfirmasi.',
            'items' => [$item->id => ['condition_in' => 'Normal', 'return_issue' => 'Terlambat dikembalikan']],
        ])->assertRedirect(route('rentals.index'));
        $rental->refresh()->load('items', 'user');
        $this->assertEquals(505000, $rental->grand_total);
        $this->assertSame(10, $this->laptop->fresh()->stock);
        $html = view('rentals.invoice-pdf', ['rental' => $rental, 'settings' => []])->render();
        foreach (['Rp 480.000', 'Rp 25.000', 'Rp 505.000', '05/10/2026, 08/10/2026', 'Laptop, charger, tas', 'Normal, gores halus pada casing', 'Terlambat dikembalikan', 'Denda keterlambatan sesuai konfirmasi.', 'Keterangan Penting &amp; Aturan Sewa'] as $text) {
            $this->assertStringContainsString(html_entity_decode($text), html_entity_decode($html));
        }
        $this->get(route('rentals.invoice', $rental))->assertOk()->assertHeader('content-type', 'application/pdf');
        $pdf = Pdf::loadHTML($html)->setPaper('a4')->output();
        $this->assertStringStartsWith('%PDF-', $pdf);
    }
}
