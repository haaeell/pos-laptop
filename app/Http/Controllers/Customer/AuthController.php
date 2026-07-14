<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    protected function safeRedirect(Request $request): string
    {
        $redirect = $request->input('redirect');

        return (is_string($redirect) && str_starts_with($redirect, '/') && !str_starts_with($redirect, '//'))
            ? $redirect
            : '/';
    }

    public function showRegister()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:customers,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('customers')->login($customer);
        $request->session()->regenerate();

        return redirect($this->safeRedirect($request))->with('success', 'Selamat datang, ' . $customer->name . '!');
    }

    public function showLogin()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::guard('customers')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])->onlyInput('email');
        }

        $customer = Auth::guard('customers')->user();

        if (!$customer->is_active) {
            Auth::guard('customers')->logout();
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        $request->session()->regenerate();

        return redirect($this->safeRedirect($request));
    }

    public function logout(Request $request)
    {
        Auth::guard('customers')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
