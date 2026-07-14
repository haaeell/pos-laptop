@extends('layouts.app')

@section('title', 'Artikel')

@section('content')
    <div class="mx-auto bg-white rounded-xl">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Artikel</h1>
                <nav class="text-sm text-slate-500 mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-indigo-600">Dashboard</a></li>
                        <li>/</li>
                        <li class="text-slate-700 font-medium">Artikel</li>
                    </ol>
                </nav>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('article-categories.index') }}"
                    class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-tags"></i> Kategori
                </a>
                <button onclick="openCreateModal()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    + Tulis Artikel
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-rose-50 text-rose-700 rounded-xl border border-rose-200 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow border overflow-x-auto px-4 py-5">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="p-2 text-left">#</th>
                        <th class="p-2 text-left">Cover</th>
                        <th class="p-2 text-left">Judul</th>
                        <th class="p-2 text-left">Kategori</th>
                        <th class="p-2 text-left">Status</th>
                        <th class="p-2 text-left">Dilihat</th>
                        <th class="p-2 text-left">Tanggal</th>
                        <th class="p-2 text-center" width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($articles as $i => $article)
                        <tr class="border-b">
                            <td class="p-2">{{ $i + 1 }}</td>
                            <td class="p-2">
                                <div class="w-14 h-14 rounded-lg border border-slate-200 overflow-hidden bg-slate-100 flex items-center justify-center">
                                    @if ($article->cover_image)
                                        <img src="{{ asset('storage/' . $article->cover_image) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-image text-slate-300"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="p-2 font-medium max-w-xs">{{ $article->title }}</td>
                            <td class="p-2">{{ $article->category?->name ?? '-' }}</td>
                            <td class="p-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $article->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $article->status === 'published' ? 'Terbit' : 'Draft' }}
                                </span>
                            </td>
                            <td class="p-2">{{ $article->views }}</td>
                            <td class="p-2 text-nowrap">{{ $article->published_at?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="p-2 text-center text-nowrap space-x-2">
                                <button onclick='openEditModal(@json($article))'
                                    class="px-3 py-1 bg-yellow-400 rounded hover:bg-yellow-500">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button onclick="deleteArticle({{ $article->id }})"
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-400">Belum ada artikel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL -->
    <div id="articleModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl border border-slate-200 overflow-y-auto max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 id="articleModalTitle" class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-newspaper text-indigo-600"></i>
                    <span>Tulis Artikel</span>
                </h2>
                <button onclick="closeArticleModal()" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-slate-100">
                    <i class="fa-solid fa-xmark text-slate-500"></i>
                </button>
            </div>

            <form id="articleForm" method="POST" class="px-6 py-5 space-y-4" enctype="multipart/form-data">
                @csrf
                <div id="articleMethodField"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Judul Artikel</label>
                        <input type="text" name="title" id="a_title" required
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600 mb-1 block">Kategori</label>
                        <select name="article_category_id" id="a_category"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
                            <option value="">-- Tanpa Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Ringkasan (untuk SEO &amp; kartu artikel)</label>
                    <textarea name="excerpt" id="a_excerpt" rows="2" maxlength="500"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Cover Artikel</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-xl border flex items-center justify-center bg-slate-50 overflow-hidden shrink-0">
                            <img id="a_cover_preview" class="w-full h-full object-cover hidden">
                            <i id="a_cover_icon" class="fa-solid fa-image text-slate-300 text-2xl"></i>
                        </div>
                        <input type="file" name="cover_image" accept="image/*" class="block text-sm text-slate-600
                            file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                            file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Konten Artikel</label>
                    <textarea name="content" id="a_content" rows="6"
                        class="w-full rounded-xl border border-slate-300 p-2 text-sm"></textarea>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Status</label>
                    <select name="status" id="a_status"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30">
                        <option value="draft">Draft</option>
                        <option value="published">Terbitkan</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeArticleModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <form id="deleteArticleForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script>
        $('#a_content').summernote({
            height: 260,
            placeholder: 'Tulis konten artikel...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview']],
            ],
            callbacks: {
                onImageUpload: function (files) {
                    const formData = new FormData();
                    formData.append('image', files[0]);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                    fetch('{{ route('articles.upload-content-image') }}', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.url) $('#a_content').summernote('insertImage', data.url);
                        })
                        .catch(() => alert('Gagal mengunggah gambar.'));
                },
            },
        });

        function resetArticleForm() {
            document.getElementById('articleForm').reset();
            document.getElementById('articleForm').action = '{{ route('articles.store') }}';
            document.getElementById('articleMethodField').innerHTML = '';
            document.getElementById('articleModalTitle').querySelector('span').innerText = 'Tulis Artikel';
            $('#a_content').summernote('code', '');
            document.getElementById('a_cover_preview').classList.add('hidden');
            document.getElementById('a_cover_icon').classList.remove('hidden');
        }

        function openCreateModal() {
            resetArticleForm();
            document.getElementById('articleModal').classList.remove('hidden');
            document.getElementById('articleModal').classList.add('flex');
        }

        function closeArticleModal() {
            document.getElementById('articleModal').classList.add('hidden');
            document.getElementById('articleModal').classList.remove('flex');
        }

        function openEditModal(article) {
            resetArticleForm();
            document.getElementById('articleModalTitle').querySelector('span').innerText = 'Ubah Artikel';
            document.getElementById('articleForm').action = `/articles/${article.id}`;
            document.getElementById('articleMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('a_title').value = article.title;
            document.getElementById('a_category').value = article.article_category_id ?? '';
            document.getElementById('a_excerpt').value = article.excerpt ?? '';
            document.getElementById('a_status').value = article.status;
            $('#a_content').summernote('code', article.content ?? '');

            if (article.cover_image) {
                document.getElementById('a_cover_preview').src = `/storage/${article.cover_image}`;
                document.getElementById('a_cover_preview').classList.remove('hidden');
                document.getElementById('a_cover_icon').classList.add('hidden');
            }

            document.getElementById('articleModal').classList.remove('hidden');
            document.getElementById('articleModal').classList.add('flex');
        }

        document.getElementById('articleForm').addEventListener('submit', function () {
            $('#a_content').val($('#a_content').summernote('code'));
        });

        function deleteArticle(id) {
            if (!confirm('Hapus artikel ini?')) return;
            const form = document.getElementById('deleteArticleForm');
            form.action = `/articles/${id}`;
            form.submit();
        }
    </script>
@endpush
