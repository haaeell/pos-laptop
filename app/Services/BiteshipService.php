<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BiteshipService
{
    protected const BASE_URL = 'https://api.biteship.com';

    protected function apiKey(): string
    {
        return Setting::get('biteship_api_key', '');
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey());
    }

    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => $this->apiKey(),
        ])->baseUrl(self::BASE_URL);
    }

    /**
     * Auto-generates and persists the webhook secret token the first time
     * it's needed, so Settings always has one to display/reuse.
     */
    public function webhookToken(): string
    {
        $token = Setting::get('biteship_webhook_token');

        if (!$token) {
            $token = Str::random(40);
            Setting::updateOrCreate(['key' => 'biteship_webhook_token'], ['value' => $token]);
        }

        return $token;
    }

    public function webhookUrl(): string
    {
        return url('/biteship/webhook/' . $this->webhookToken());
    }

    /**
     * @return array List of areas: [{id, name, ...}]
     */
    public function searchArea(string $query): array
    {
        $response = $this->client()->get('/v1/maps/areas', [
            'countries' => 'ID',
            'input' => $query,
            'type' => 'single',
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json('areas', []);
    }

    public function getCouriers(): array
    {
        $response = $this->client()->get('/v1/couriers');

        if (!$response->successful()) {
            return [];
        }

        return $response->json('couriers', []);
    }

    /**
     * @param array $items [['name' => ..., 'value' => ..., 'quantity' => ..., 'weight' => ...], ...]
     * @return array pricing options from Biteship
     */
    public function getRates(string $originAreaId, string $destinationAreaId, array $items, array $courierCodes = []): array
    {
        if (empty($courierCodes)) {
            $courierCodes = ['jne', 'jnt', 'sicepat', 'anteraja', 'ninja'];
        }

        $response = $this->client()->post('/v1/rates/couriers', [
            'origin_area_id' => $originAreaId,
            'destination_area_id' => $destinationAreaId,
            'couriers' => implode(',', $courierCodes),
            'items' => $items,
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json('pricing', []);
    }

    public function createOrder(Order $order): array
    {
        $order->loadMissing('items.product');

        $items = $order->items->map(fn ($item) => [
            'name' => $item->product_name,
            'value' => (int) round($item->price),
            'quantity' => $item->qty,
            'weight' => $item->product?->weight ?? 1000,
        ])->toArray();

        $payload = [
            'origin_contact_name' => Setting::get('biteship_origin_contact_name', ''),
            'origin_contact_phone' => Setting::get('biteship_origin_contact_phone', ''),
            'origin_address' => Setting::get('biteship_origin_address', ''),
            'origin_area_id' => $order->origin_area_id,
            'destination_contact_name' => $order->recipient_name,
            'destination_contact_phone' => $order->recipient_phone,
            'destination_address' => $order->address_detail,
            'destination_area_id' => $order->destination_area_id,
            'courier_company' => $order->courier_company,
            'courier_type' => $order->courier_type,
            'delivery_type' => 'now',
            'order_note' => $order->notes,
            'reference_id' => $order->order_number,
            'items' => $items,
        ];

        $response = $this->client()->post('/v1/orders', $payload);

        if (!$response->successful()) {
            throw new \RuntimeException('Biteship API error: ' . $response->body());
        }

        $data = $response->json();

        $order->update([
            'biteship_order_id' => $data['id'] ?? null,
            'courier_waybill_id' => $data['courier']['waybill_id'] ?? null,
            'courier_tracking_id' => $data['courier']['tracking_id'] ?? null,
            'shipment_status' => $data['status'] ?? null,
        ]);

        return $data;
    }

    /**
     * Retrieves the order (not the dedicated /trackings endpoint — that one
     * returns "Failed to retrieve tracking number" until a courier has
     * actually scanned the waybill in their own system; retrieving the
     * order itself is reliable from the moment it's created and already
     * includes the courier's tracking history).
     */
    public function trackOrder(Order $order): array
    {
        if (!$order->biteship_order_id) {
            return [];
        }

        $response = $this->client()->get('/v1/orders/' . $order->biteship_order_id);

        if (!$response->successful()) {
            return [];
        }

        return $response->json();
    }
}
