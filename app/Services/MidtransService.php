<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    protected function configurationIssue(): ?string
    {
        $serverKey = Setting::get('midtrans_server_key', '');
        $clientKey = Setting::get('midtrans_client_key', '');
        $isProduction = Setting::get('midtrans_is_production', '0') === '1';

        if (!$serverKey || !$clientKey) {
            return null;
        }

        $serverUsesSandboxKey = str_starts_with($serverKey, 'SB-Mid-');
        $clientUsesSandboxKey = str_starts_with($clientKey, 'SB-Mid-');

        if ($serverUsesSandboxKey !== $clientUsesSandboxKey) {
            return 'Server Key dan Client Key Midtrans tidak satu mode. Pastikan keduanya sama-sama Sandbox atau sama-sama Live/Production.';
        }

        if ($isProduction && $serverUsesSandboxKey) {
            return 'Mode Midtrans Production aktif, tapi key yang dipakai masih Sandbox. Matikan Mode Produksi atau ganti ke Live Server Key dan Live Client Key.';
        }

        if (!$isProduction && !$serverUsesSandboxKey) {
            return 'Mode Midtrans Sandbox aktif, tapi key yang dipakai terlihat seperti Live/Production. Aktifkan Mode Produksi atau ganti ke Sandbox Server Key dan Sandbox Client Key.';
        }

        return null;
    }

    protected function configure(): void
    {
        if ($issue = $this->configurationIssue()) {
            throw new \RuntimeException($issue);
        }

        $settings = Setting::pluck('value', 'key');

        Config::$serverKey = $settings['midtrans_server_key'] ?? '';
        Config::$clientKey = $settings['midtrans_client_key'] ?? '';
        Config::$isProduction = ($settings['midtrans_is_production'] ?? '0') === '1';
        Config::$is3ds = true;
    }

    public function isConfigured(): bool
    {
        $settings = Setting::pluck('value', 'key');

        return filled($settings['midtrans_server_key'] ?? null)
            && filled($settings['midtrans_client_key'] ?? null);
    }

    public function getClientKey(): string
    {
        return Setting::get('midtrans_client_key', '');
    }

    public function isProductionMode(): bool
    {
        return Setting::get('midtrans_is_production', '0') === '1';
    }

    public function createSnapToken(Order $order): string
    {
        $this->configure();

        $order->loadMissing(['items', 'customer']);

        $itemDetails = $order->items->map(function ($item) {
            return [
                'id' => (string) $item->product_id,
                'price' => (int) round($item->price),
                'quantity' => $item->qty,
                'name' => mb_substr($item->product_name, 0, 50),
            ];
        })->toArray();

        // Midtrans expects item_details to sum to gross_amount, so shipping
        // (if any) needs its own line — grand_total now includes it.
        if ((float) $order->shipping_cost > 0) {
            $itemDetails[] = [
                'id' => 'shipping',
                'price' => (int) round($order->shipping_cost),
                'quantity' => 1,
                'name' => mb_substr('Ongkos Kirim - ' . ($order->courier_service_name ?? ''), 0, 50),
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) round($order->grand_total),
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $order->recipient_name,
                'phone' => $order->recipient_phone,
                'email' => $order->customer->email,
                'shipping_address' => [
                    'first_name' => $order->recipient_name,
                    'phone' => $order->recipient_phone,
                    'address' => $order->address_detail,
                    'city' => $order->city,
                ],
            ],
        ];

        return Snap::getSnapToken($params);
    }

    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = Setting::get('midtrans_server_key', '');

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $signatureKey);
    }
}
