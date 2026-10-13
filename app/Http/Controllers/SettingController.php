<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\BiteshipService;
use Illuminate\Http\Request;
use Storage;

class SettingController extends Controller
{
    public function index(BiteshipService $biteship)
    {
        $settings = Setting::pluck('value', 'key');
        $midtransNotificationUrl = url('/midtrans/notification');
        $biteshipWebhookUrl = $biteship->webhookUrl();

        return view('setting.index', compact('settings', 'midtransNotificationUrl', 'biteshipWebhookUrl'));
    }

    public function update(Request $request)
    {
        foreach (['sales', 'rental'] as $type) {
            $key = "payroll_{$type}_share_value";
            if ($request->input("payroll_{$type}_share_mode") !== 'fixed' || ! $request->has($key)) {
                continue;
            }

            $raw = mb_strtolower(preg_replace('/\s+/', '', (string) $request->input($key)));
            if (preg_match('/^(?:rp)?(\d{1,3}(?:\.\d{3})+|\d+)(rb|ribu|jt|juta)?$/', $raw, $matches)) {
                $multiplier = match ($matches[2] ?? '') {
                    'rb', 'ribu' => 1000,
                    'jt', 'juta' => 1000000,
                    default => 1,
                };
                $request->merge([$key => (int) str_replace('.', '', $matches[1]) * $multiplier]);
            }
        }

        $request->validate([
            'referral_discount_amount' => 'nullable|numeric|min:0',
            'payroll_sales_share_mode' => 'sometimes|required|in:percentage,fixed',
            'payroll_sales_share_value' => 'required_with:payroll_sales_share_mode|numeric|min:0|max:' . ($request->input('payroll_sales_share_mode') === 'percentage' ? '100' : '9999999999999'),
            'payroll_rental_share_mode' => 'sometimes|required|in:percentage,fixed',
            'payroll_rental_share_value' => 'required_with:payroll_rental_share_mode|numeric|min:0|max:' . ($request->input('payroll_rental_share_mode') === 'percentage' ? '100' : '9999999999999'),
        ]);

        $data = $request->except('_token');
        $fileFields = ['logo', 'favicon_512', 'favicon_48', 'favicon_32'];

        foreach ($data as $key => $value) {

            if (in_array($key, $fileFields) && $request->hasFile($key)) {
                $value = $request->file($key)->store('settings', 'public');
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Pengaturan berhasil disimpan');
    }

    public function searchBiteshipArea(Request $request, BiteshipService $biteship)
    {
        $query = $request->input('q', '');

        if (mb_strlen($query) < 3) {
            return response()->json(['areas' => []]);
        }

        return response()->json(['areas' => $biteship->searchArea($query)]);
    }
}
