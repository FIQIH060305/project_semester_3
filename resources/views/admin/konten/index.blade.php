@extends('layouts.admin')

@section('title', 'Kelola Konten Edukasi')
@section('subtitle', 'Kelola video olahraga, resep, dan artikel')

@section('content')
@if (session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-brand-card border border-brand-soft rounded-3xl p-6">
        <h2 class="text-sm font-bold text-brand-deep mb-4">Daftar Konten</h2>
        <div class="space-y-3">
            @forelse ($kontenList as $konten)
                <div class="flex items-center gap-4 p-4 rounded-2xl border border-brand-soft">
                    <div class="w-16 h-16 rounded-xl bg-brand-bg flex items-center justify-center overflow-hidden shrink-0">
                        @if ($konten->thumbnail)
                            <img src="{{ asset('storage/' . $konten->thumbnail) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-brand-dark/30 text-xs">No Img</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full bg-brand-purple/10 text-brand-purple">
                            {{ str_replace('_', ' ', $konten->tipe_konten) }}
                        </span>
                        <p class="text-sm font-medium text-brand-deep mt-1.5 truncate">{{ $konten->judul }}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('admin.konten.edit', $konten->id_konten) }}" class="text-brand hover:text-brand-dark text-xs font-semibold">Edit</a>
                        <form action="{{ route('admin.konten.destroy', $konten->id_konten) }}" method="POST"
                              onsubmit="return confirm('Hapus konten ini?')">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-600 text-xs font-semibold">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-brand-dark/40 text-center py-8">Belum ada konten. Tambahkan lewat form di samping.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-brand-card border border-brand-soft rounded-3xl p-6">
        <h2 class="text-sm font-bold text-brand-deep mb-4">Tambah Konten Baru</h2>

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
        <label class="block text-xs font-semibold text-brand-deep mb-1.5">Tipe Konten</label>
        <select name="tipe_konten" id="tipeKonten" required class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
            <option value="video_olahraga">Video Olahraga</option>
            <option value="video_resep">Video Resep Makanan Sehat</option>
            <option value="artikel">Artikel Kesehatan</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-brand-deep mb-1.5">Judul</label>
        <input type="text" name="judul" value="{{ old('judul') }}" required
            class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
    </div>
    <div>
        <label class="block text-xs font-semibold text-brand-deep mb-1.5">Deskripsi Singkat</label>
        <textarea name="deskripsi" rows="2"
            class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">{{ old('deskripsi') }}</textarea>
        <p class="text-[10px] text-brand-dark/50 mt-1">Ringkasan pendek, muncul di kartu sebelum dibuka.</p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-brand-deep mb-1.5">Kategori (opsional)</label>
        <input type="text" name="kategori" value="{{ old('kategori') }}" placeholder="Contoh: Pemanasan, Diet Rendah Gula"
            class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
    </div>
    <!-- Muncul HANYA untuk tipe video -->
    <div id="fieldYoutube">
        <label class="block text-xs font-semibold text-brand-deep mb-1.5">Link YouTube</label>
        <input type="url" id="ytInput" name="url_video_youtube" value="{{ old('url_video_youtube') }}" placeholder="https://youtube.com/watch?v=..."
            class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
        <div id="ytPreviewWrap" class="mt-2 hidden">
            <iframe id="ytPreview" class="w-full aspect-video rounded-xl border border-brand-soft" src="" allowfullscreen></iframe>
        </div>
    </div>

    <!-- Muncul HANYA untuk tipe artikel -->
    <div id="fieldArtikel" class="hidden">
        <label class="block text-xs font-semibold text-brand-deep mb-1.5">Isi Artikel Lengkap</label>
        <textarea name="konten_teks" rows="8"
            placeholder="Tulis isi artikel di sini. Bisa hasil rangkuman dari sumber berita kesehatan, tulis ulang dengan bahasamu sendiri."
            class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">{{ old('konten_teks') }}</textarea>
        <p class="text-[10px] text-brand-dark/50 mt-1">Pengguna akan membaca teks ini langsung di web, tidak diarahkan keluar.</p>
    </div>

    <div>
        <label class="block text-xs font-semibold text-brand-deep mb-1.5">Upload Thumbnail (gambar)</label>
        <input type="file" name="thumbnail" accept="image/*"
            class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-xs text-brand-deep focus:outline-none focus:border-brand">
        <p class="text-[10px] text-brand-dark/50 mt-1">Format JPG/PNG, maksimal 2MB.</p>
    </div>
    <button type="submit" class="w-full py-3 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
        Simpan Konten
    </button>
</form>
    </div>
</div>

<script>
const tipeSelect = document.getElementById('tipeKonten');
const fieldYoutube = document.getElementById('fieldYoutube');
const fieldArtikel = document.getElementById('fieldArtikel');

function toggleFields() {
    if (tipeSelect.value === 'artikel') {
        fieldYoutube.classList.add('hidden');
        fieldArtikel.classList.remove('hidden');
    } else {
        fieldYoutube.classList.remove('hidden');
        fieldArtikel.classList.add('hidden');
    }
}
tipeSelect.addEventListener('change', toggleFields);
toggleFields(); // jalankan sekali saat halaman dibuka

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
</script>
@endsection