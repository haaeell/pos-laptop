@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Manajemen User</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">User</li>
                    </ol>
                </nav>
            </div>
            <button onclick="openCreateModal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                + Tambah User
            </button>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table id="datatable" class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3 text-center">Role</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $i => $user)
                        <tr class="hover:bg-slate-50 border-b border-slate-100">
                            <td class="px-4 py-3 text-center">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium">
                                {{ $user->name }}
                                @if ($user->id === auth()->id())
                                    <span class="ml-1 text-xs text-indigo-400">(Saya)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($user->role === 'super_admin')
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">
                                        <i class="fa-solid fa-crown mr-1"></i>Super Admin
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">
                                        <i class="fa-solid fa-user-shield mr-1"></i>Admin
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($user->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Aktif</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center space-x-1">
                                <button onclick='openEditModal(@json($user))'
                                    class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500 transition">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                @if ($user->id !== auth()->id())
                                    <button onclick="deleteUser({{ $user->id }})"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL -->
    <div id="userModal" class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl">

            <div class="flex items-center gap-3 px-6 py-4 border-b">
                <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h2 id="modalTitle" class="text-base font-bold text-slate-800">Tambah User</h2>
            </div>

            <form id="userForm" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Nama</label>
                    <input type="text" name="name" id="userName" required
                        class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" id="userEmail" required
                        class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        Password <span id="passwordHint" class="text-slate-400 normal-case font-normal">(kosongkan jika
                            tidak diubah)</span>
                    </label>
                    <input type="password" name="password" id="userPassword"
                        class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-600 uppercase tracking-wide">Role</label>
                    <select name="role" id="userRole" required
                        class="w-full px-4 py-2.5 mt-1 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>

                <div id="activeContainer" class="hidden">
                    <label class="inline-flex items-center text-sm text-slate-600">
                        <input type="checkbox" name="is_active" id="userActive" checked
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 font-bold">User Aktif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#datatable').DataTable();

                window.openCreateModal = function () {
                    $('#userModal').removeClass('hidden');
                    $('#modalTitle').text('Tambah User');
                    $('#userForm').attr('action', '/users');
                    $('#methodField').val('');
                    $('#userForm')[0].reset();
                    $('#activeContainer').addClass('hidden');
                    $('#passwordHint').show();
                    $('#userPassword').attr('required', true);
                }

                window.openEditModal = function (data) {
                    $('#userModal').removeClass('hidden');
                    $('#modalTitle').text('Edit User');
                    $('#userForm').attr('action', `/users/${data.id}`);
                    $('#methodField').val('PUT');

                    $('#userName').val(data.name);
                    $('#userEmail').val(data.email);
                    $('#userPassword').val('').removeAttr('required');
                    $('#userRole').val(data.role);
                    $('#userActive').prop('checked', data.is_active);
                    $('#activeContainer').removeClass('hidden');
                    $('#passwordHint').show();
                }

                window.closeModal = function () {
                    $('#userModal').addClass('hidden');
                }

                window.deleteUser = function (id) {
                    Swal.fire({
                        title: 'Hapus User?',
                        text: 'User akan dihapus permanen!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonText: 'Batal',
                        confirmButtonText: 'Ya, Hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/users/${id}`;
                            form.innerHTML = `
                                    <input type="hidden" name="_token" value="${$('meta[name=csrf-token]').attr('content')}">
                                    <input type="hidden" name="_method" value="DELETE">
                                `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection