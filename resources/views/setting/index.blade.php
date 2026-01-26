@extends('layouts.app')

@section('title', 'Setting Toko')

@section('content')
    <div class="max-w-4xl mx-auto">

        <!-- HEADER -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Pengaturan Toko</h1>
            <p class="text-sm text-slate-500 mt-1">
                Kelola identitas toko, informasi, dan lokasi peta
            </p>
        </div>

        <form method="POST" action="/settings" enctype="multipart/form-data"
            class="bg-white rounded-2xl shadow-sm border p-6 space-y-8">
            @csrf

            <!-- ================= LOGO ================= -->
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Logo Toko</h3>

                <div class="flex items-center gap-5">
                    <div class="w-24 h-24 rounded-xl border flex items-center justify-center bg-slate-50 overflow-hidden">
                        @if(isset($settings['logo']))
                            <img src="{{ asset('storage/' . $settings['logo']) }}" class="w-full h-full object-contain">
                        @else
                            <i class="fa-solid fa-image text-slate-300 text-3xl"></i>
                        @endif
                    </div>

                    <div>
                        <input type="file" name="logo" class="block text-sm text-slate-600
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-xl file:border-0
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100">
                        <p class="text-xs text-slate-500 mt-1">
                            PNG / JPG, disarankan ukuran persegi
                        </p>
                    </div>
                </div>
            </div>

            <!-- ================= INFORMASI TOKO ================= -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-600">
                        Nama Toko
                    </label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-store absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="nama_toko" value="{{ $settings['nama_toko'] ?? '' }}"
                            placeholder="Nama toko anda" class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm
                                                  focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase text-slate-600">
                        Jam Operasional
                    </label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="jam_buka" value="{{ $settings['jam_buka'] ?? '' }}"
                            placeholder="09.00 - 21.00" class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm
                                                  focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                </div>

            </div>

            <!-- ================= ALAMAT ================= -->
            <div>
                <label class="text-xs font-semibold uppercase text-slate-600">
                    Alamat Toko
                </label>
                <textarea name="alamat" rows="3" placeholder="Alamat lengkap toko"
                    class="w-full mt-1 rounded-xl border px-4 py-3 text-sm
                                             focus:ring-2 focus:ring-indigo-500/30">{{ $settings['alamat'] ?? '' }}</textarea>
            </div>

            <!-- ================= DESKRIPSI ================= -->
            <div>
                <label class="text-xs font-semibold uppercase text-slate-600">
                    Deskripsi Singkat
                </label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang toko"
                    class="w-full mt-1 rounded-xl border px-4 py-3 text-sm
                                             focus:ring-2 focus:ring-indigo-500/30">{{ $settings['deskripsi'] ?? '' }}</textarea>
            </div>

            <!-- ================= ACTION ================= -->
            <div class="pt-4 flex justify-end">
                <button class="px-6 py-2.5 bg-gradient-to-br from-indigo-600 to-blue-600
                                       text-white rounded-xl text-sm font-semibold
                                       hover:opacity-90 transition">
                    <i class="fa-solid fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
@endsection