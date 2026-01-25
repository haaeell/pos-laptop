@extends('layouts.app')

@section('title', 'Transaksi Baru')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-indigo-600"></i>
                    Transaksi Baru
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Pilih produk, tentukan harga final, lalu simpan transaksi
                </p>
            </div>
        </div>

        <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
            @csrf

            <div class="grid grid-cols-12 gap-6">

                <!-- ================= LEFT : PRODUCT LIST ================= -->
                <div class="col-span-12 lg:col-span-8">

                    <div class="border rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100 text-slate-700">
                                <tr>
                                    <th class="p-3">Produk</th>
                                    <th class="p-3 text-center">Harga Beli</th>
                                    <th class="p-3 text-center">Harga Jual</th>
                                    <th class="p-3 text-center">Harga Final</th>
                                    <th class="p-3 text-center">Profit</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="itemTable">
                                <!-- Empty state -->
                                <tr id="emptyRow">
                                    <td colspan="6" class="p-6 text-center text-slate-400">
                                        <i class="fa-solid fa-box-open text-2xl mb-2"></i>
                                        <div>Belum ada produk</div>
                                        <div class="text-xs">Klik “Tambah Produk” untuk mulai</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="addItemBtn"
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2
                                                                                                                                                                                                                                                                   bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <i class="fa-solid fa-plus"></i>
                        Tambah Produk
                    </button>


                </div>

                <!-- ================= RIGHT : SUMMARY ================= -->
                <div class="col-span-12 lg:col-span-4">
                    <div
                        class="relative overflow-hidden rounded-2xl border bg-white/80 backdrop-blur shadow-lg shadow-indigo-100 p-5 space-y-4">

                        <div class="absolute -top-16 -right-16 w-32 h-32 bg-indigo-500/20 rounded-full blur-3xl"></div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-slate-800">Ringkasan Transaksi</h3>
                                <p class="text-[11px] text-slate-500">Preview sebelum disimpan</p>
                            </div>
                            <i class="fa-solid fa-receipt text-indigo-500"></i>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-medium text-slate-600">
                                Metode Pembayaran
                            </label>

                            <input type="hidden" name="payment_method" id="paymentMethod" value="cash">

                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" data-value="cash" class="payment-card flex items-center gap-2 px-3 py-2 rounded-xl border
                                                                   bg-indigo-50 border-indigo-500 ring-2 ring-indigo-200
                                                                   text-indigo-700 font-semibold shadow-sm transition">
                                    <div
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-100 text-green-600">
                                        <i class="fa-solid fa-money-bill-wave text-sm"></i>
                                    </div>
                                    <span class="text-xs">Cash</span>
                                    <i class="fa-solid fa-check ml-auto text-[10px]"></i>
                                </button>

                                <button type="button" data-value="transfer" class="payment-card flex items-center gap-2 px-3 py-2 rounded-xl border
                                                                   bg-white text-slate-600 transition hover:shadow-sm">
                                    <div
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                        <i class="fa-solid fa-building-columns text-sm"></i>
                                    </div>
                                    <span class="text-xs">Transfer</span>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div
                                class="rounded-xl p-4 bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-indigo-200">
                                            Grand Total
                                        </p>
                                        <p class="text-2xl font-bold mt-0.5">
                                            Rp <span id="grandText">0</span>
                                        </p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                        <i class="fa-solid fa-wallet"></i>
                                    </div>
                                </div>
                                <input type="hidden" name="grand_total" id="grandTotal">
                            </div>

                            <div class="rounded-xl border px-4 py-3 bg-slate-50">
                                <p class="text-[11px] text-slate-500 flex items-center gap-1">
                                    <i class="fa-solid fa-chart-line"></i>
                                    Profit
                                </p>
                                <p id="benefitText" class="text-base font-bold text-green-600 mt-1">
                                    Rp 0
                                </p>
                                <input type="hidden" name="benefit" id="benefit">
                            </div>
                        </div>

                        <div id="bonusContainer" class="rounded-xl border px-4 py-3 bg-slate-50 space-y-2">
                            <p class="text-[11px] text-slate-500 flex items-center gap-1">
                                <i class="fa-solid fa-gift"></i>
                                Bonus Produk
                            </p>

                            <select name="bonus_products[]" id="bonusSelect" class="w-full select2 text-xs" multiple>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-purchase="{{ $product->purchase_price }}">
                                        {{ $product->product_code }} - {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="bonusInfo" class="hidden text-[11px] text-slate-600 space-y-1"></div>
                        </div>

                        <button type="button" id="submitBtn" class="w-full py-2.5 rounded-xl text-sm text-white font-semibold
                           bg-gradient-to-r from-indigo-500 to-indigo-600
                           hover:from-indigo-600 hover:to-indigo-700
                           shadow-md shadow-indigo-200
                           active:scale-[0.98] transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-sm"></i>
                            Simpan Transaksi
                        </button>


                        <p class="text-center text-[10px] text-slate-400">
                            Pastikan data sudah benar sebelum menyimpan
                        </p>
                    </div>
                </div>

            </div>
        </form>

        @if(session('success_sale_id'))
            <div id="successModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl p-6 w-full max-w-sm space-y-4 shadow-xl">

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800">Transaksi Berhasil</h3>
                            <p class="text-xs text-slate-500">Invoice berhasil dibuat</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('sales.invoice.pdf', session('success_sale_id')) }}" target="_blank"
                            class="flex-1 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold text-center">
                            Cetak Invoice
                        </a>

                        <button onclick="document.getElementById('successModal').remove()"
                            class="flex-1 py-2 rounded-xl border text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif


        <div id="confirmModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50">

            <div class="bg-white rounded-2xl w-full max-w-sm p-6 space-y-4 shadow-xl">

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-800">
                            Konfirmasi Transaksi
                        </h3>
                        <p class="text-xs text-slate-500">
                            Pastikan data sudah benar
                        </p>
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 p-3 text-sm">
                    <div class="flex justify-between">
                        <span>Total</span>
                        <span class="font-semibold">Rp <span id="modalGrand">0</span></span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500 mt-1">
                        <span>Pembayaran</span>
                        <span id="modalPayment">Cash</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" id="cancelConfirm" class="flex-1 py-2 rounded-xl border text-sm">
                        Batal
                    </button>

                    <button type="button" id="confirmSubmit"
                        class="flex-1 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold">
                        Ya, Simpan
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(function () {

            const $form = $('#saleForm')
            const $submitBtn = $('#submitBtn')
            const $modal = $('#confirmModal')

            $('#submitBtn').on('click', function () {
                $('#modalGrand').text($('#grandText').text())

                const payment =
                    $('#paymentMethod').val() === 'transfer' ? 'Transfer' : 'Cash'
                $('#modalPayment').text(payment)

                $modal.removeClass('hidden').addClass('flex')
            })

            $('#cancelConfirm').on('click', function () {
                $modal.addClass('hidden').removeClass('flex')
            })

            $('#confirmSubmit').on('click', function () {
                $(this).prop('disabled', true).text('Menyimpan...')
                $submitBtn
                    .prop('disabled', true)
                    .addClass('opacity-60 cursor-not-allowed')

                $form.submit()
            })

            const products = @json($products);
            let index = 0;
            const $table = $('#itemTable')

            // ================= UTIL =================
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka || 0)
            }

            function cleanNumber(val) {
                return Number(String(val).replace(/\./g, '')) || 0
            }

            // ================= ADD ITEM =================
            $('#addItemBtn').on('click', function () {

                $('#emptyRow').remove()

                const row = `
                                                                                                                                                                                                                <tr class="border-t">
                                                                                                                                                                                                                    <td class="p-2">
                                                                                                                                                                                                                        <select name="items[${index}][product_id]"
                                                                                                                                                                                                                            class="w-full px-2 py-1 border rounded product-select select2">
                                                                                                                                                                                                                            <option value="">-- Pilih Produk --</option>
                                                                                                                                                                                                                            ${products.map(p => `
                                                                                                                                                                                                                                <option value="${p.id}"
                                                                                                                                                                                                                                    data-purchase="${p.purchase_price}"
                                                                                                                                                                                                                                    data-selling="${p.selling_price}">
                                                                                                                                                                                                                                    ${p.product_code} - ${p.name}
                                                                                                                                                                                                                                </option>
                                                                                                                                                                                                                            `).join('')}
                                                                                                                                                                                                                        </select>
                                                                                                                                                                                                                    </td>

                                                                                                                                                                                                                   <td class="p-2">
                                                                                                                                                                                <div class="relative">
                                                                                                                                                                                    <i class="fa-solid fa-cart-shopping absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                                                                                                                                    <input type="text"
                                                                                                                                                                                        class="w-full pl-9 px-2 py-1 bg-slate-100 border rounded purchase-text"
                                                                                                                                                                                        readonly>
                                                                                                                                                                                    <input type="hidden" name="items[${index}][purchase_price]" class="purchase-val">
                                                                                                                                                                                </div>
                                                                                                                                                                            </td>

                                                                                                                                                                            <td class="p-2">
                                                                                                                                                                                <div class="relative">
                                                                                                                                                                                    <i class="fa-solid fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                                                                                                                                    <input type="text"
                                                                                                                                                                                        class="w-full pl-9 px-2 py-1 bg-slate-100 border rounded selling-text"
                                                                                                                                                                                        readonly>
                                                                                                                                                                                    <input type="hidden" name="items[${index}][selling_price]" class="selling-val">
                                                                                                                                                                                </div>
                                                                                                                                                                            </td>

                                                                                                                                                                            <td class="p-2">
                                                                                                                                                                                <div class="relative">
                                                                                                                                                                                    <i class="fa-solid fa-pen absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"></i>
                                                                                                                                                                                    <input type="text"
                                                                                                                                                                                        class="w-full pl-9 px-2 py-1 border rounded final-text">
                                                                                                                                                                                    <input type="hidden" name="items[${index}][final_price]" class="final-val">
                                                                                                                                                                                </div>
                                                                                                                                                                            </td>

                                                                                                                                                                            <td class="p-2">
                                                                                                                                                                                <div class="relative">
                                                                                                                                                                                    <i class="fa-solid fa-coins absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                                                                                                                                                                    <input type="text"
                                                                                                                                                                                        class="w-full pl-9 px-2 py-1 bg-slate-100 border rounded benefit text-right"
                                                                                                                                                                                        readonly>
                                                                                                                                                                                </div>
                                                                                                                                                                            </td>


                                                                                                                                                                                                                    <td class="p-2 text-center">
                                                                                                                                                                                                                        <button type="button"
                                                                                                                                                                                                                            class="remove-btn px-3 py-1 bg-red-500 text-white rounded">
                                                                                                                                                                                                                            <i class="fa-solid fa-trash"></i>
                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                    </td>
                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                `

                $table.append(row)

                $table.find('.select2').last().select2({
                    placeholder: 'Cari kode / nama produk',
                    dropdownParent: $table
                })

                index++
            })

            $('#bonusSelect').select2({
                placeholder: 'Pilih bonus produk',
                allowClear: true
            })

            $('#bonusSelect').on('change', function () {
                calculate()
            })

            // ================= SELECT PRODUCT =================
            $(document).on('change', '.product-select', function () {
                const $row = $(this).closest('tr')
                const $opt = $(this).find(':selected')

                const purchase = Number($opt.data('purchase') || 0)
                const selling = Number($opt.data('selling') || 0)

                $row.find('.purchase-text').val(formatRupiah(purchase))
                $row.find('.purchase-val').val(purchase)

                $row.find('.selling-text').val(formatRupiah(selling))
                $row.find('.selling-val').val(selling)

                $row.find('.final-text').val(formatRupiah(selling))
                $row.find('.final-val').val(selling)

                calculate()
            })

            // ================= FINAL PRICE INPUT =================
            $(document).on('input', '.final-text', function () {
                const val = cleanNumber($(this).val())
                $(this).val(formatRupiah(val))
                $(this).closest('tr').find('.final-val').val(val)
                calculate()
            })

            // ================= REMOVE =================
            $(document).on('click', '.remove-btn', function () {
                $(this).closest('tr').remove()
                calculate()

                if (!$table.children().length) {
                    $table.html(`
                                                                                                                                                                                                                        <tr id="emptyRow">
                                                                                                                                                                                                                            <td colspan="6" class="p-6 text-center text-slate-400">
                                                                                                                                                                                                                                <i class="fa-solid fa-box-open text-2xl mb-2"></i>
                                                                                                                                                                                                                                <div>Belum ada produk</div>
                                                                                                                                                                                                                                <div class="text-xs">Klik “Tambah Produk” untuk mulai</div>
                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                    `)
                }
            })

            // ================= CALCULATE =================
            function calculate() {
                let total = 0
                let profit = 0
                let bonusCost = 0

                // ================= ITEM PROFIT =================
                $table.find('tr').each(function () {
                    const purchase = Number($(this).find('.purchase-val').val() || 0)
                    const final = Number($(this).find('.final-val').val() || 0)
                    const benefit = final - purchase

                    total += final
                    profit += benefit

                    const $benefit = $(this).find('.benefit')
                    $benefit
                        .val(formatRupiah(benefit))
                        .removeClass('text-red-600 text-green-600')
                        .addClass(benefit < 0 ? 'text-red-600' : 'text-green-600')
                })

                // ================= BONUS PROFIT (PENTING) =================
                const $bonusInfo = $('#bonusInfo')
                $bonusInfo.empty()

                $('#bonusSelect option:selected').each(function () {
                    const name = $(this).text()
                    const purchase = Number($(this).data('purchase') || 0)

                    bonusCost += purchase

                    $bonusInfo.append(`
                                                                <div class="flex justify-between">
                                                                    <span>🎁 ${name}</span>
                                                                    <span class="text-red-600">-Rp ${formatRupiah(purchase)}</span>
                                                                </div>
                                                            `)
                })

                if (bonusCost > 0) {
                    profit -= bonusCost
                    $bonusInfo.removeClass('hidden')
                } else {
                    $bonusInfo.addClass('hidden')
                }

                // ================= OUTPUT =================
                $('#grandTotal').val(total)
                $('#benefit').val(profit)

                $('#grandText').text(formatRupiah(total))

                $('#benefitText')
                    .text(formatRupiah(profit))
                    .removeClass('text-red-600 text-green-600')
                    .addClass(profit < 0 ? 'text-red-600' : 'text-green-600')
            }


            $(document).on('click', '.payment-card', function () {
                const $btn = $(this)

                $('.payment-card')
                    .removeClass('bg-indigo-50 border-indigo-500 ring-2 ring-indigo-200 text-indigo-700 font-semibold shadow-md')
                    .find('.fa-check').remove()

                $btn.addClass('bg-indigo-50 border-indigo-500 ring-2 ring-indigo-200 text-indigo-700 font-semibold shadow-md')

                if ($btn.find('.fa-check').length === 0) {
                    $btn.append('<i class="fa-solid fa-check ml-auto text-xs"></i>')
                }

                $('#paymentMethod').val($btn.data('value'))
            })

        })
    </script>
@endpush