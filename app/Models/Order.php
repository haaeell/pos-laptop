<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'grand_total',
        'items_subtotal',
        'shipping_cost',
        'recipient_name',
        'recipient_phone',
        'province',
        'city',
        'district',
        'address_detail',
        'notes',
        'expires_at',
        'snap_token',
        'midtrans_payment_type',
        'courier_company',
        'courier_type',
        'courier_service_name',
        'origin_area_id',
        'destination_area_id',
        'biteship_order_id',
        'courier_waybill_id',
        'courier_tracking_id',
        'courier_routing_code',
        'shipment_status',
        'paid_at',
        'shipped_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'grand_total' => 'decimal:2',
        'items_subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    const STATUS_LABELS = [
        'pending_payment' => 'Menunggu Pembayaran',
        'paid' => 'Sudah Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'expired' => 'Kedaluwarsa',
        'failed' => 'Gagal',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function trackingHistories()
    {
        return $this->hasMany(ShipmentTrackingHistory::class)->orderBy('created_at');
    }

    public function hasShipment(): bool
    {
        return filled($this->biteship_order_id);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'processing', 'shipped', 'completed']);
    }

}
