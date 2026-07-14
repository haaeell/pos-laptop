<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customer_addresses')) {
            Schema::create('customer_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
                $table->string('label')->default('Rumah');
                $table->string('recipient_name');
                $table->string('recipient_phone');
                $table->string('province');
                $table->string('city');
                $table->string('district');
                $table->string('postal_code')->nullable();
                $table->string('area_id')->nullable();
                $table->text('address_detail');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'weight')) {
                $table->unsignedInteger('weight')->default(1000)->after('stock');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'items_subtotal' => fn () => $table->decimal('items_subtotal', 15, 2)->nullable()->after('grand_total'),
                'shipping_cost' => fn () => $table->decimal('shipping_cost', 15, 2)->nullable()->after('items_subtotal'),
                'courier_company' => fn () => $table->string('courier_company')->nullable()->after('shipping_cost'),
                'courier_type' => fn () => $table->string('courier_type')->nullable()->after('courier_company'),
                'courier_service_name' => fn () => $table->string('courier_service_name')->nullable()->after('courier_type'),
                'origin_area_id' => fn () => $table->string('origin_area_id')->nullable()->after('courier_service_name'),
                'destination_area_id' => fn () => $table->string('destination_area_id')->nullable()->after('origin_area_id'),
                'biteship_order_id' => fn () => $table->string('biteship_order_id')->nullable()->after('destination_area_id'),
                'courier_waybill_id' => fn () => $table->string('courier_waybill_id')->nullable()->after('biteship_order_id'),
                'courier_tracking_id' => fn () => $table->string('courier_tracking_id')->nullable()->after('courier_waybill_id'),
                'shipment_status' => fn () => $table->string('shipment_status')->nullable()->after('courier_tracking_id'),
            ];

            foreach ($columns as $column => $add) {
                if (!Schema::hasColumn('orders', $column)) {
                    $add();
                }
            }
        });

        if (!Schema::hasTable('shipment_tracking_histories')) {
            Schema::create('shipment_tracking_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->string('status');
                $table->string('note')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking_histories');

        Schema::table('orders', function (Blueprint $table) {
            $drop = array_filter([
                'items_subtotal',
                'shipping_cost',
                'courier_company',
                'courier_type',
                'courier_service_name',
                'origin_area_id',
                'destination_area_id',
                'biteship_order_id',
                'courier_waybill_id',
                'courier_tracking_id',
                'shipment_status',
            ], fn ($col) => Schema::hasColumn('orders', $col));

            if ($drop) {
                $table->dropColumn($drop);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'weight')) {
                $table->dropColumn('weight');
            }
        });

        Schema::dropIfExists('customer_addresses');
    }
};
