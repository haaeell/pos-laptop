@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Penjualan</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="/home" class="hover:text-indigo-600">Dashboard</a>
                        </li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Penjualan</li>
                    </ol>
                </nav>
            </div>

            <a href="{{ route('sales.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                + Transaksi Baru
            </a>
        </div>

        <form class="flex flex-wrap items-end gap-3 mb-3">
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Dari Tanggal</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-calendar-day text-xs"></i>
                    </div>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none text-slate-700">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider ml-1">Sampai Tanggal</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-calendar-check text-xs"></i>
                    </div>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none text-slate-700">
                </div>
            </div>

            <button type="submit"
                class="h-[40px] px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm shadow-indigo-100 transition-all flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i>
                Terapkan
            </button>
        </form>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="datatable" class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">#</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Invoice</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Tanggal</th>
                            <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Grand Total</th>
                            @if (Auth::user()->role == 'super_admin')
                                <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Profit</th>
                            @endif
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Pembayaran</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">Sales</th>
                            <th class="px-4 py-4 font-semibold text-right whitespace-nowrap">Fee Sales</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 uppercase">
                        @foreach ($sales as $i => $sale)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="px-4 py-4 text-slate-500 whitespace-nowrap">{{ $loop->iteration }}</td>

                                <td class="px-4 py-4 font-bold text-indigo-600 whitespace-nowrap">
                                    <span class="bg-indigo-50 px-2 py-1 rounded text-xs">#{{ $sale->invoice_number }}</span>
                                </td>

                                <td class="px-4 py-4 text-slate-600 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $sale->created_at->format('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $sale->created_at->format('H:i') }}
                                            WIB</span>
                                    </div>
                                </td>

                                <td class="px-4 py-4 font-bold text-slate-800 whitespace-nowrap">
                                    Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                </td>

                                @if (Auth::user()->role == 'super_admin')
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $sale->benefit >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $sale->benefit >= 0 ? '+' : '' }} Rp {{ number_format($sale->benefit, 0, ',', '.') }}
                                        </span>
                                    </td>
                                @endif

                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="px-3 py-1 rounded-lg text-[10px] font-bold tracking-wider bg-slate-100 text-slate-600 border border-slate-200 w-fit">
                                            {{ strtoupper($sale->payment_method) }}
                                        </span>

                                        @php
                                            $statusColor = match ($sale->payment_status) {
                                                'paid' => 'bg-emerald-100 text-emerald-700',
                                                'partial' => 'bg-amber-100 text-amber-700',
                                                default => 'bg-rose-100 text-rose-700',
                                            };
                                            $statusLabel = match ($sale->payment_status) {
                                                'paid' => 'Lunas',
                                                'partial' => 'Sebagian',
                                                default => 'Hutang',
                                            };
                                            $isOverdue = $sale->due_date && $sale->due_date->isPast() && $sale->payment_status !== 'paid';
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold w-fit {{ $statusColor }}">
                                            {{ $statusLabel }}
                                        </span>
                                        @if ($sale->payment_status !== 'paid')
                                            <span class="text-[10px] text-slate-400 normal-case">
                                                Sisa: Rp {{ number_format($sale->remaining_amount, 0, ',', '.') }}
                                            </span>
                                            @if ($sale->due_date)
                                                <span class="text-[10px] normal-case {{ $isOverdue ? 'text-rose-500 font-semibold' : 'text-slate-400' }}">
                                                    <i class="fa-solid fa-calendar-day"></i>
                                                    {{ $isOverdue ? 'Lewat jatuh tempo' : 'Jatuh tempo' }}: {{ $sale->due_date->format('d M Y') }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($sale->salesPerson)
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-slate-700">{{ $sale->salesPerson->name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $sale->salesPerson->phone }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs italic text-slate-400">Tanpa Sales</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    <span class="text-rose-600 font-bold">
                                        Rp {{ number_format($sale->fee_sales ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        <button onclick="openSaleDetail({{ $sale->id }})"
                                            class="inline-flex items-center justify-center w-9 h-9 text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm shadow-blue-100"
                                            title="Lihat Detail">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </button>

                                        @if ($sale->payment_status !== 'paid')
                                            <button type="button"
                                                onclick="openPayModal({{ $sale->id }}, '{{ $sale->invoice_number }}', {{ $sale->remaining_amount }})"
                                                class="inline-flex items-center justify-center w-9 h-9 text-amber-600 bg-amber-50 rounded-xl hover:bg-amber-600 hover:text-white transition-all duration-200 shadow-sm shadow-amber-100"
                                                title="Bayar Cicilan">
                                                <i class="fa-solid fa-hand-holding-dollar text-sm"></i>
                                            </button>
                                        @endif

                                        <a href="{{ url("/sales/{$sale->id}/invoice-pdf") }}" target="_blank"
                                            class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-800 hover:text-white transition-all duration-200 shadow-sm shadow-slate-200"
                                            title="Cetak Invoice">
                                            <i class="fa-solid fa-print text-sm"></i>
                                        </a>

                                        <form id="delete-form-{{ $sale->id }}" action="{{ route('sales.destroy', $sale->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete({{ $sale->id }}, '{{ $sale->invoice_number }}')"
                                                class="inline-flex items-center justify-center w-9 h-9 text-rose-600 bg-rose-50 rounded-xl hover:bg-rose-600 hover:text-white transition-all duration-200 shadow-sm shadow-rose-100"
                                                title="Hapus Transaksi">
                                                <i class="fa-solid fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="saleModal"
        class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden transform transition-all">
            <div class="flex items-center justify-between px-8 py-6 bg-slate-50 border-b border-slate-100">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">Detail Transaksi</h2>
                    <p id="modalInvoice" class="text-sm text-indigo-600 font-medium mt-1"></p>
                </div>
                <button onclick="closeSaleDetail()"
                    class="w-10 h-10 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-4 rounded-2xl bg-slate-50 border">
                        <p class="text-xs font-semibold text-slate-400 uppercase">Sales</p>
                        <p id="modalSales" class="font-bold text-slate-700 mt-1">-</p>
                        <p id="modalSalesPhone" class="text-xs text-slate-400"></p>
                    </div>

                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100">
                        <p class="text-xs font-semibold text-rose-600 uppercase">Fee Sales</p>
                        <p id="modalFeeSales" class="text-xl font-extrabold text-rose-600 mt-1">Rp 0</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 overflow-hidden shadow-sm bg-slate-50/30">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-slate-500 border-b border-slate-100">
                                <th class="px-4 py-3 text-left font-semibold">Item Produk</th>
                                <th class="px-4 py-3 text-right font-semibold">Harga Jual</th>
                                @if (Auth::user()->role == 'super_admin')
                                    <th class="px-4 py-3 text-right font-semibold text-emerald-600">Profit</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="modalItems" class="divide-y divide-slate-100 bg-white text-slate-700">
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 grid {{ Auth::user()->role == 'super_admin' ? 'grid-cols-2' : 'grid-cols-1' }} gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Penjualan</p>
                        <p id="modalTotal" class="text-xl font-extrabold text-slate-800 mt-1"></p>
                    </div>
                    @if (Auth::user()->role == 'super_admin')
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Total Keuntungan</p>
                            <p id="modalProfit" class="text-xl font-extrabold text-emerald-700 mt-1"></p>
                        </div>
                    @endif
                </div>

                <div id="modalInstallmentInfo" class="hidden mt-4 p-4 rounded-2xl bg-amber-50 border border-amber-100">
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-2">Info Pembayaran Sebagian / Hutang</p>
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-[11px] text-slate-500">Sudah Dibayar</p>
                            <p id="modalPaidAmount" class="font-bold text-slate-800"></p>
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-500">Sisa Tagihan</p>
                            <p id="modalRemainingAmount" class="font-bold text-rose-600"></p>
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-500">Jatuh Tempo</p>
                            <p id="modalDueDate" class="font-bold text-slate-800"></p>
                        </div>
                    </div>
                    <a id="modalCollateralLink" href="#" target="_blank"
                        class="hidden mt-3 inline-flex items-center gap-2 text-xs font-semibold text-amber-700 hover:text-amber-800">
                        <i class="fa-solid fa-id-card"></i>
                        Lihat Jaminan (KTP)
                    </a>

                    <div class="mt-3">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Riwayat Pembayaran</p>
                        <div id="modalPaymentHistory" class="space-y-1 text-xs"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="payModal" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Bayar Cicilan</h3>
                    <p id="payModalInvoice" class="text-xs text-slate-500"></p>
                </div>
            </div>

            <p class="text-xs text-slate-500 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2">
                Sisa tagihan: <span id="payModalRemaining" class="font-bold text-amber-700">Rp 0</span>
            </p>

            <form id="payForm" method="POST">
                @csrf
                <div class="space-y-2 mb-3">
                    <label class="text-[11px] font-medium text-slate-600">Jumlah Bayar</label>
                    <input type="text" id="payAmountText" placeholder="0"
                        class="w-full px-3 py-2 rounded-xl border text-sm focus:ring-2 focus:ring-amber-200">
                    <input type="hidden" name="amount" id="payAmount">
                </div>

                <div class="space-y-2 mb-4">
                    <label class="text-[11px] font-medium text-slate-600">Tanggal Bayar</label>
                    <input type="date" name="paid_at" id="payDate"
                        class="w-full px-3 py-2 rounded-xl border text-sm focus:ring-2 focus:ring-amber-200">
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closePayModal()" class="flex-1 py-2 rounded-xl border text-sm">Batal</button>
                    <button type="submit"
                        class="flex-1 py-2 rounded-xl bg-amber-600 text-white text-sm font-semibold hover:bg-amber-700">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const isSuperAdmin = {{ Auth::user()->role === 'super_admin' ? 'true' : 'false' }};

        function openSaleDetail(id) {
            fetch(`/sales/${id}/detail`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('saleModal').classList.remove('hidden')
                    document.getElementById('saleModal').classList.add('flex')

                    document.getElementById('modalInvoice').innerText = data.invoice + ' · ' + data.date
                    document.getElementById('modalTotal').innerText = 'Rp ' + data.grand_total
                    document.getElementById('modalSales').innerText = data.sales_name ?? 'Tanpa Sales'
                    document.getElementById('modalSalesPhone').innerText = data.sales_phone ?? ''
                    document.getElementById('modalFeeSales').innerText = 'Rp ' + data.fee_sales

                    if (isSuperAdmin) {
                        document.getElementById('modalProfit').innerText = 'Rp ' + data.benefit
                    }

                    const $installmentInfo = document.getElementById('modalInstallmentInfo')
                    if (data.payment_status !== 'paid') {
                        document.getElementById('modalPaidAmount').innerText = 'Rp ' + data.paid_amount
                        document.getElementById('modalRemainingAmount').innerText = 'Rp ' + data.remaining_amount
                        document.getElementById('modalDueDate').innerText = data.due_date ?? '-'

                        const $collateralLink = document.getElementById('modalCollateralLink')
                        if (data.collateral_url) {
                            $collateralLink.href = data.collateral_url
                            $collateralLink.classList.remove('hidden')
                        } else {
                            $collateralLink.classList.add('hidden')
                        }

                        const $history = document.getElementById('modalPaymentHistory')
                        if (data.payments && data.payments.length) {
                            $history.innerHTML = data.payments.map(p => `
                                <div class="flex justify-between items-center bg-white border border-amber-100 rounded-lg px-3 py-1.5">
                                    <div>
                                        <span class="font-semibold text-slate-700">Rp ${p.amount}</span>
                                        <span class="text-slate-400"> · ${p.date}</span>
                                    </div>
                                    <span class="text-slate-400">${p.by ?? '-'}</span>
                                </div>
                            `).join('')
                        } else {
                            $history.innerHTML = '<p class="text-slate-400 italic">Belum ada pembayaran tercatat</p>'
                        }

                        $installmentInfo.classList.remove('hidden')
                    } else {
                        $installmentInfo.classList.add('hidden')
                    }

                    let rows = ''

                    data.items.forEach(item => {
                        rows += `
                                <tr class="border-t">
                                    <td class="px-3 py-2">${item.name}</td>
                                    <td class="px-3 py-2 text-right">Rp ${item.price}</td>
                                    ${isSuperAdmin ? `<td class="px-3 py-2 text-right">${item.benefit}</td>` : ''}
                                </tr>
                            `
                    })

                    data.bonuses.forEach(item => {
                        rows += `
                                <tr class="border-t text-green-600">
                                    <td class="px-3 py-2">🎁 ${item.name}</td>
                                    <td class="px-3 py-2 text-right">Rp 0</td>
                                    ${isSuperAdmin ? `<td class="px-3 py-2 text-right">${item.benefit}</td>` : ''}
                                </tr>
                            `
                    })

                    document.getElementById('modalItems').innerHTML = rows
                })
        }

        function closeSaleDetail() {
            document.getElementById('saleModal').classList.add('hidden')
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka || 0)
        }

        function cleanNumber(val) {
            return Number(String(val).replace(/\./g, '')) || 0
        }

        let payModalRemaining = 0

        function openPayModal(id, invoice, remaining) {
            payModalRemaining = remaining
            document.getElementById('payForm').action = `/sales/${id}/pay`
            document.getElementById('payModalInvoice').innerText = '#' + invoice
            document.getElementById('payModalRemaining').innerText = 'Rp ' + formatRupiah(remaining)
            document.getElementById('payAmountText').value = ''
            document.getElementById('payAmount').value = ''
            document.getElementById('payDate').value = new Date().toISOString().slice(0, 10)
            document.getElementById('payModal').classList.remove('hidden')
            document.getElementById('payModal').classList.add('flex')
        }

        function closePayModal() {
            document.getElementById('payModal').classList.add('hidden')
            document.getElementById('payModal').classList.remove('flex')
        }

        $(document).on('input', '#payAmountText', function () {
            let val = cleanNumber($(this).val())
            if (val > payModalRemaining) {
                val = payModalRemaining
                showPayNotification(`Jumlah bayar tidak boleh melebihi sisa tagihan (Rp ${formatRupiah(payModalRemaining)})!`)
            }
            $(this).val(formatRupiah(val))
            $('#payAmount').val(val)
        })

        function showPayNotification(message) {
            const notification = $(`
                <div class="fixed top-4 right-4 bg-yellow-500 text-white px-4 py-3 rounded-lg shadow-lg z-50">
                    ${message}
                </div>
            `)
            $('body').append(notification)
            setTimeout(() => notification.fadeOut(300, function () { $(this).remove() }), 3000)
        }

        $(document).on('submit', '#payForm', function (e) {
            if (cleanNumber($('#payAmountText').val()) <= 0) {
                e.preventDefault()
                showPayNotification('Jumlah bayar wajib diisi!')
            }
        })

        $(document).ready(function () {
            $('#datatable').DataTable()
        })

        function confirmDelete(id, invoice) {
            Swal.fire({
                title: 'Hapus Transaksi?',
                html: `Invoice <strong>#${invoice}</strong> akan dihapus permanen.<br><span class="text-sm text-gray-500">Status produk akan dikembalikan ke <b>tersedia</b>.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit()
                }
            })
        }
    </script>
@endpush