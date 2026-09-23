@extends('layouts.admin')

@section('title', 'Edit Konten')
@section('subtitle', 'Perbarui detail konten edukasi')

@section('content')
<div class="max-w-lg bg-brand-card border border-brand-soft rounded-3xl p-6">

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
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Tipe Konten</label>
            <select name="tipe_konten" required class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                <option value="video_olahraga" {{ $konten->tipe_konten == 'video_olahraga' ? 'selected' : '' }}>Video Olahraga</option>
                <option value="video_resep" {{ $konten->tipe_konten == 'video_resep' ? 'selected' : '' }}>Video Resep Makanan Sehat</option>
                <option value="artikel" {{ $konten->tipe_konten == 'artikel' ? 'selected' : '' }}>Artikel Kesehatan</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Judul</label>
            <input type="text" name="judul" value="{{ old('judul', $konten->judul) }}" required
                class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
        </div>
        <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Deskripsi</label>
            <textarea name="deskripsi" rows="3"
                class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">{{ old('deskripsi', $konten->deskripsi) }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Kategori (opsional)</label>
            <input type="text" name="kategori" value="{{ old('kategori', $konten->kategori) }}"
                class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
        </div>
        <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Link YouTube</label>
            <input type="url" name="url_video_youtube" value="{{ old('url_video_youtube', $konten->url_video_youtube) }}"
                class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Isi Artikel Lengkap (khusus tipe Artikel)</label>
            <textarea name="konten_teks" rows="8"
                class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">{{ old('konten_teks', $konten->konten_teks) }}</textarea>
</div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Thumbnail Saat Ini</label>
            @if ($konten->thumbnail)
                <img src="{{ asset('storage/' . $konten->thumbnail) }}" class="w-24 h-24 object-cover rounded-xl border border-brand-soft mb-2">
            @else
                <p class="text-xs text-brand-dark/40 mb-2">Belum ada thumbnail.</p>
            @endif
            <input type="file" name="thumbnail" accept="image/*"
                class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-xs text-brand-deep focus:outline-none focus:border-brand">
            <p class="text-[10px] text-brand-dark/50 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 py-3 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.konten.index') }}" class="py-3 px-5 border border-brand-soft text-brand-deep font-bold text-sm rounded-xl hover:bg-brand-bg transition text-center">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection