@extends('layouts.app')

@section('title', 'Kurir')

@push('styles')
    <style>
        .switch { position: relative; display: inline-block; width: 42px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .switch-slider {
            position: absolute; inset: 0; cursor: pointer;
            background-color: #cbd5e1; transition: .2s; border-radius: 999px;
        }
        .switch-slider::before {
            content: ""; position: absolute; height: 18px; width: 18px;
            left: 3px; bottom: 3px; background-color: #fff; transition: .2s; border-radius: 50%;
        }
        .switch input:checked + .switch-slider { background-color: #10b981; }
        .switch input:checked + .switch-slider::before { transform: translateX(18px); }
    </style>
@endpush

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Kurir</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="/home" class="hover:text-indigo-600">Dashboard</a>
                        </li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Kurir</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th width="5%">#</th>
                        <th width="12%">Logo</th>
                        <th>Kode</th>
                        <th>Nama Kurir</th>
                        <th width="12%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($couriers as $i => $courier)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <form action="{{ route('couriers.update-logo', $courier->id) }}" method="POST"
                                    enctype="multipart/form-data" class="flex items-center gap-2">
                                    @csrf
                                    <label class="w-12 h-9 rounded-lg border flex items-center justify-center bg-slate-50 overflow-hidden cursor-pointer"
                                        title="Klik untuk ganti logo">
                                        @if($courier->logo)
                                            <img src="{{ asset('storage/' . $courier->logo) }}" class="w-full h-full object-contain">
                                        @else
                                            <i class="fa-solid fa-truck text-slate-300"></i>
                                        @endif
                                        <input type="file" name="logo" accept="image/*" class="hidden"
                                            onchange="this.form.submit()">
                                    </label>
                                </form>
                            </td>
                            <td class="font-mono text-xs text-slate-500 uppercase">{{ $courier->code }}</td>
                            <td class="font-medium">{{ $courier->name }}</td>
                            <td class="text-center">
                                <form action="{{ route('couriers.toggle-active', $courier->id) }}" method="POST">
                                    @csrf
                                    <label class="switch">
                                        <input type="checkbox" onchange="this.form.submit()" {{ $courier->is_active ? 'checked' : '' }}>
                                        <span class="switch-slider"></span>
                                    </label>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function () {
                $('#datatable').DataTable()
            })
        </script>
    @endpush
@endsection
