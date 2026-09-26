@extends('layouts.admin')

@section('title', 'Kelola Konten Edukasi')
@section('subtitle', 'Kelola video olahraga, resep makanan sehat, dan artikel kesehatan')

@section('content')
@if (session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold flex items-center gap-2">
        <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Kolom Kiri: Daftar Konten -->
    <div class="lg:col-span-2 bg-white border border-brand-border rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-extrabold text-gray-800">Daftar Konten</h2>
            <span class="text-xs font-bold text-gray-400">Total: {{ $kontenList->count() }} Konten</span>
        </div>

        <div class="space-y-3">
            @forelse ($kontenList as $konten)
                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl border border-brand-border hover:border-brand/30 hover:shadow-sm transition bg-white">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-16 h-16 rounded-xl bg-brand-bg border border-brand-border flex items-center justify-center overflow-hidden shrink-0">
                            @php
                                $ytThumbnail = null;
                                if ($konten->url_video_youtube && in_array($konten->tipe_konten, ['video_olahraga', 'video_resep'])) {
                                    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $konten->url_video_youtube, $matches);
                                    if (isset($matches[1])) {
                                        $ytThumbnail = 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
                                    }
                                }
                            @endphp

                            @if ($ytThumbnail)
                                <!-- Thumbnail Otomatis dari YouTube -->
                                <img src="{{ $ytThumbnail }}" class="w-full h-full object-cover">
                            @elseif ($konten->thumbnail)
                                <!-- Thumbnail dari Upload (Artikel) -->
                                <img src="{{ asset('storage/' . $konten->thumbnail) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-gray-400 text-[10px] font-bold uppercase">No Image</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <span class="inline-block text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full bg-brand/10 text-brand mb-1">
                                {{ str_replace('_', ' ', $konten->tipe_konten) }}
                            </span>
                            <p class="text-sm font-bold text-gray-800 truncate leading-snug">{{ $konten->judul }}</p>
                            @if($konten->kategori)
                                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $konten->kategori }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center gap-2 shrink-0">
                        <!-- Tombol Edit (Biru) -->
                        <a href="{{ route('admin.konten.edit', $konten->id_konten) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand hover:bg-brand-dark text-white text-xs font-bold shadow-sm shadow-brand/20 transition active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>

                        <!-- Tombol Hapus (Merah) -->
                        <form id="deleteForm-{{ $konten->id_konten }}" action="{{ route('admin.konten.destroy', $konten->id_konten) }}" method="POST" class="inline">
                            @csrf
                            <button type="button" onclick="openDeleteModal('{{ $konten->id_konten }}', '{{ addslashes($konten->judul) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-xs font-bold shadow-sm shadow-red-500/20 transition active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 border border-dashed border-brand-border rounded-2xl">
                    <p class="text-xs font-semibold text-gray-400">Belum ada konten tersimpan.</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Gunakan formulir di samping untuk menambahkan konten baru.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Kolom Kanan: Form Tambah Konten -->
    <div class="bg-white border border-brand-border rounded-3xl p-6 shadow-sm h-fit">
        <h2 class="text-base font-extrabold text-gray-800 mb-4">Tambah Konten Baru</h2>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.konten.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Tipe Konten</label>
                <select name="tipe_konten" id="tipeKonten" required 
                        class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">
                    <option value="video_olahraga">Video Olahraga</option>
                    <option value="video_resep">Video Resep Makanan Sehat</option>
                    <option value="artikel">Artikel Kesehatan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Judul Konten</label>
                <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Masukkan judul konten..."
                       class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="2" placeholder="Ringkasan singkat konten..."
                          class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">{{ old('deskripsi') }}</textarea>
                <p class="text-[10px] text-gray-400 mt-1">Muncul pada kartu preview sebelum dibuka.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori (Opsional)</label>
                <input type="text" name="kategori" value="{{ old('kategori') }}" placeholder="Contoh: Pemanasan, Rendah Kalori"
                       class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">
            </div>

            <!-- Field khusus Video YouTube -->
            <div id="fieldYoutube">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Link YouTube</label>
                <input type="url" id="ytInput" name="url_video_youtube" value="{{ old('url_video_youtube') }}" placeholder="https://youtube.com/watch?v=..."
                       class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">
                <div id="ytPreviewWrap" class="mt-3 hidden">
                    <iframe id="ytPreview" class="w-full aspect-video rounded-xl border border-brand-border" src="" allowfullscreen></iframe>
                </div>
            </div>

            <!-- Field khusus Artikel -->
            <div id="fieldArtikel" class="hidden">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Isi Artikel Lengkap</label>
                <textarea name="konten_teks" rows="8" placeholder="Tuliskan materi artikel lengkap di sini..."
                          class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">{{ old('konten_teks') }}</textarea>
                <p class="text-[10px] text-gray-400 mt-1">Pembaca dapat membaca artikel langsung di dalam aplikasi.</p>
            </div>

            <!-- Field Thumbnail Gambar (Hanya untuk Artikel) -->
            <div id="fieldThumbnail" class="hidden">
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Thumbnail Gambar Artikel</label>
                <input type="file" name="thumbnail" accept="image/*"
                       class="w-full bg-brand-bg border border-brand-border rounded-xl px-3 py-2 text-xs font-semibold text-gray-600 focus:outline-none focus:border-brand transition">
                <p class="text-[10px] text-gray-400 mt-1">Format JPG, PNG, JPEG (Maksimal 2MB).</p>
            </div>

            <button type="submit" class="w-full py-3 bg-brand hover:bg-brand-dark text-white font-bold text-xs rounded-xl shadow-md shadow-brand/20 transition active:scale-95">
                Simpan Konten
            </button>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl text-center">
        <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h3 class="text-base font-extrabold text-gray-800 mb-2">Hapus Konten Ini?</h3>
        <p class="text-sm text-gray-500 mb-6">
            Konten <span id="deleteModalJudul" class="font-semibold text-gray-700"></span> akan dihapus permanen dan tidak bisa dikembalikan.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 py-3 rounded-xl border border-brand-border text-gray-600 font-bold text-sm hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="button" onclick="confirmDelete()"
                    class="flex-1 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-sm transition">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
const tipeSelect = document.getElementById('tipeKonten');
const fieldYoutube = document.getElementById('fieldYoutube');
const fieldArtikel = document.getElementById('fieldArtikel');
const fieldThumbnail = document.getElementById('fieldThumbnail');

function toggleFields() {
    if (tipeSelect.value === 'artikel') {
        fieldYoutube.classList.add('hidden');
        fieldArtikel.classList.remove('hidden');
        fieldThumbnail.classList.remove('hidden'); // Tampilkan thumbnail untuk Artikel
    } else {
        fieldYoutube.classList.remove('hidden');
        fieldArtikel.classList.add('hidden');
        fieldThumbnail.classList.add('hidden'); // Sembunyikan thumbnail untuk Video Olahraga & Resep
    }
}
tipeSelect.addEventListener('change', toggleFields);
toggleFields();

document.getElementById('ytInput').addEventListener('input', function () {
    const url = this.value;
    const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    const wrap = document.getElementById('ytPreviewWrap');
    const iframe = document.getElementById('ytPreview');

    if (match) {
        iframe.src = 'https://www.youtube.com/embed/' + match[1];
        wrap.classList.remove('hidden');
    } else {
        wrap.classList.add('hidden');
        iframe.src = '';
    }
});

// ===== Modal konfirmasi hapus =====
let formIdToDelete = null;

function openDeleteModal(id, judul) {
    formIdToDelete = id;
    document.getElementById('deleteModalJudul').textContent = '"' + judul + '"';
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    formIdToDelete = null;
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

function confirmDelete() {
    if (formIdToDelete) {
        document.getElementById('deleteForm-' + formIdToDelete).submit();
    }
}

document.getElementById('deleteModal').addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endsection