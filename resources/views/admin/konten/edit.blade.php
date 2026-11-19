@extends('layouts.admin')

@section('title', 'Edit Konten')
@section('subtitle', 'Perbarui detail konten edukasi')

@section('content')
<div class="max-w-2xl bg-white border border-brand-border rounded-3xl p-6 shadow-sm">

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs">
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.konten.update', $konten->id_konten) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Tipe Konten</label>
            <select name="tipe_konten" id="tipeKonten" required class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">
                <option value="video_olahraga" {{ old('tipe_konten', $konten->tipe_konten) == 'video_olahraga' ? 'selected' : '' }}>Video Olahraga</option>
                <option value="video_resep" {{ old('tipe_konten', $konten->tipe_konten) == 'video_resep' ? 'selected' : '' }}>Video Resep Makanan Sehat</option>
                <option value="artikel" {{ old('tipe_konten', $konten->tipe_konten) == 'artikel' ? 'selected' : '' }}>Artikel Kesehatan</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Judul</label>
            <input type="text" name="judul" value="{{ old('judul', $konten->judul) }}" required
                class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Publish</label>
            <input type="date" name="tanggal_publish" id="tanggalPublish"
                value="{{ old('tanggal_publish', $konten->tanggal_publish ? \Carbon\Carbon::parse($konten->tanggal_publish)->format('Y-m-d') : date('Y-m-d')) }}"
                min="{{ date('Y-m-d') }}"
                required
                class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">
            <p class="text-[10px] text-gray-400 mt-1">Hari ini atau tanggal setelahnya (sebelum hari ini ditolak).</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi</label>
            <textarea name="deskripsi" rows="3"
                class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">{{ old('deskripsi', $konten->deskripsi) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori (opsional)</label>
            <input type="text" name="kategori" value="{{ old('kategori', $konten->kategori) }}"
                class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">
        </div>

        <div id="fieldYoutube">
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Link YouTube</label>
            <input type="url" id="ytInput" name="url_video_youtube" value="{{ old('url_video_youtube', $konten->url_video_youtube) }}"
                class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">
            <div id="ytPreviewWrap" class="mt-3 {{ $konten->url_video_youtube ? '' : 'hidden' }}">
                @php
                    $embedUrl = '';
                    if ($konten->url_video_youtube) {
                        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $konten->url_video_youtube, $matches);
                        if (isset($matches[1])) {
                            $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                        }
                    }
                @endphp
                <iframe id="ytPreview" class="w-full aspect-video rounded-xl border border-brand-border" src="{{ $embedUrl }}" allowfullscreen></iframe>
            </div>
        </div>

        <div id="fieldArtikel" class="hidden">
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Isi Artikel Lengkap (khusus tipe Artikel)</label>
            <textarea name="konten_teks" rows="8"
                class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:outline-none focus:border-brand focus:bg-white transition">{{ old('konten_teks', $konten->konten_teks) }}</textarea>
        </div>

        <div id="fieldThumbnail" class="hidden">
            <label class="block text-xs font-bold text-gray-700 mb-1.5">Thumbnail Saat Ini</label>
            @if ($konten->thumbnail)
                <img src="{{ asset('storage/' . $konten->thumbnail) }}" class="w-24 h-24 object-cover rounded-xl border border-brand-border mb-2">
            @else
                <p class="text-xs text-gray-400 mb-2">Belum ada thumbnail.</p>
            @endif
            <input type="file" name="thumbnail" accept="image/*"
                class="w-full bg-brand-bg border border-brand-border rounded-xl px-3 py-2 text-xs font-semibold text-gray-600 focus:outline-none focus:border-brand transition">
            <p class="text-[10px] text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 py-3 bg-brand hover:bg-brand-dark text-white font-bold text-xs rounded-xl shadow-md shadow-brand/20 transition active:scale-95">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.konten.index') }}" class="py-3 px-5 border border-brand-border text-gray-700 font-bold text-xs rounded-xl hover:bg-brand-bg transition text-center">
                Batal
            </a>
        </div>
    </form>
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
        fieldThumbnail.classList.remove('hidden');
    } else {
        fieldYoutube.classList.remove('hidden');
        fieldArtikel.classList.add('hidden');
        fieldThumbnail.classList.add('hidden');
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
</script>
@endsection
