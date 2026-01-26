@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-md">

            <!-- CARD -->
            <div class="bg-white rounded-2xl shadow-sm p-6">

                <!-- HEADER -->
                <div class="text-center mb-6">
                    <div
                        class="mx-auto mb-3 w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="fa-solid fa-user text-2xl"></i>
                    </div>
                    <h2 class="text-lg font-semibold">Pengaturan Profile</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Perbarui informasi akun Anda di bawah ini
                    </p>
                </div>

                <!-- FORM -->
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- EMAIL -->
                    <div>
                        <label class="text-sm font-medium mb-1 block">
                            Email
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                class="w-full rounded-xl border pl-11 pr-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Email digunakan untuk login dan notifikasi sistem
                        </p>
                    </div>

                    <!-- PASSWORD BARU -->
                    <div>
                        <label class="text-sm font-medium mb-1 block">
                            Password Baru
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="password" name="password"
                                class="w-full rounded-xl border pl-11 pr-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Kosongkan jika tidak ingin mengubah password
                        </p>
                    </div>

                    <!-- KONFIRMASI PASSWORD -->
                    <div>
                        <label class="text-sm font-medium mb-1 block">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="password" name="password_confirmation"
                                class="w-full rounded-xl border pl-11 pr-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Ulangi password baru untuk konfirmasi
                        </p>
                    </div>

                    <!-- INFO -->
                    <div class="flex items-start gap-2 bg-indigo-50 text-indigo-700 p-3 rounded-xl text-xs">
                        <i class="fa-solid fa-circle-info mt-0.5"></i>
                        <span>
                            Demi keamanan akun, gunakan password minimal 6 karakter dan sulit ditebak.
                        </span>
                    </div>

                    <!-- BUTTON -->
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-xl
                               hover:bg-indigo-500 transition font-medium flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Perubahan
                    </button>
                </form>

            </div>
        </div>
    </div>
@endsection