<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->get();
        return view('setting.contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required',
            'phone' => 'required',
            'whatsapp_text' => 'required',
        ]);

        Contact::create([
            'label' => $request->label,
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'whatsapp_text' => $request->whatsapp_text,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Kontak WhatsApp berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required',
            'phone' => 'required',
            'whatsapp_text' => 'required',
        ]);

        Contact::findOrFail($id)->update([
            'label' => $request->label,
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'whatsapp_text' => $request->whatsapp_text,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Kontak WhatsApp berhasil diperbarui');
    }

    public function destroy($id)
    {
        Contact::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kontak WhatsApp berhasil dihapus');
    }
}
