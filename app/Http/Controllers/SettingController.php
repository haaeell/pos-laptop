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
        $biteshipWebhookUrl = $biteship->webhookUrl();

        return view('setting.index', compact('settings', 'biteshipWebhookUrl'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {

            if ($key === 'logo' && $request->hasFile('logo')) {
                $value = $request->file('logo')->store('settings', 'public');
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
