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
        $request->validate([
            'referral_discount_amount' => 'nullable|numeric|min:0',
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
