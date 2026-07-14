<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('customer.profile.edit', ['customer' => Auth::guard('customers')->user()]);
    }

    public function update(Request $request)
    {
        $customer = Auth::guard('customers')->user();

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|min:6|confirmed',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.required' => 'No. HP harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $customer->name = $data['name'];
        $customer->email = $data['email'];
        $customer->phone = $data['phone'];

        if (!empty($data['password'])) {
            $customer->password = Hash::make($data['password']);
        }

        $customer->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
