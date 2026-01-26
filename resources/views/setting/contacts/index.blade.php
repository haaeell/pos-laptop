@extends('layouts.app')

@section('title', 'Kontak WhatsApp')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Kontak WhatsApp</h1>

                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li>
                            <a href="/home" class="hover:text-indigo-600">Dashboard</a>
                        </li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Kontak WhatsApp</li>
                    </ol>
                </nav>
            </div>

            <button onclick="openCreateModal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                + Tambah
            </button>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th>No</th>
                        <th>Label</th>
                        <th>Nomor</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contacts as $i => $contact)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="font-medium">{{ $contact->label }}</td>
                                    <td>{{ $contact->phone }}</td>
                                    <td>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                                        {{ $contact->is_active
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-slate-200 text-slate-500' }}">
                                            {{ $contact->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="space-x-2">
                                        <button onclick='openEditModal(@json($contact))'
                                            class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>

                                        <button onclick="deleteContact({{ $contact->id }})"
                                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="contactModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl">

            <!-- HEADER -->
            <div class="flex items-center gap-3 px-6 py-4 border-b">
                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <h2 id="modalTitle" class="text-base font-bold text-slate-800">
                    Tambah Kontak
                </h2>
            </div>

            <!-- BODY -->
            <form id="contactForm" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <!-- LABEL -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase">Label</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="label" id="label" placeholder="Admin / Sales"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border">
                    </div>
                </div>

                <!-- PHONE -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase">Nomor WhatsApp</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="phone" id="phone" placeholder="628xxxxxxxx"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border">
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Gunakan format internasional tanpa +</p>
                </div>

                <!-- TEXT -->
                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase">Teks Chat</label>
                    <textarea name="whatsapp_text" id="text" rows="3" class="w-full rounded-xl border px-4 py-2 mt-1"
                        placeholder="Halo Admin, saya ingin bertanya..."></textarea>
                </div>

                <!-- ACTIVE -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="active" checked>
                    <label class="text-sm">Aktifkan kontak ini</label>
                </div>

                <!-- FOOTER -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl border">
                        Batal
                    </button>

                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-semibold">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#datatable').DataTable()

                const modal = $('#contactModal')
                const form = $('#contactForm')

                window.openCreateModal = function () {
                    modal.removeClass('hidden')
                    $('#modalTitle').text('Tambah Kontak WhatsApp')
                    form.attr('action', '/contacts')
                    $('#methodField').val('')
                    $('#label, #phone, #text').val('')
                    $('#active').prop('checked', true)
                }

                window.openEditModal = function (data) {
                    modal.removeClass('hidden')
                    $('#modalTitle').text('Edit Kontak WhatsApp')
                    form.attr('action', `/contacts/${data.id}`)
                    $('#methodField').val('PUT')

                    $('#label').val(data.label)
                    $('#phone').val(data.phone)
                    $('#text').val(data.whatsapp_text)
                    $('#active').prop('checked', data.is_active)
                }

                window.closeModal = function () {
                    modal.addClass('hidden')
                }

                window.deleteContact = function (id) {
                    Swal.fire({
                        title: 'Yakin?',
                        text: 'Kontak WhatsApp akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form')
                            form.method = 'POST'
                            form.action = `/contacts/${id}`
                            form.innerHTML = `
                                <input type="hidden" name="_token" value="${$('meta[name=csrf-token]').attr('content')}">
                                <input type="hidden" name="_method" value="DELETE">
                            `
                            document.body.appendChild(form)
                            form.submit()
                        }
                    })
                }
            })
        </script>
    @endpush
@endsection