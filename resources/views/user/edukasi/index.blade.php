@extends('layouts.app-user')
@section('title', 'Konten Edukasi')

@section('content')
<div class="flex items-start justify-between mb-6 flex-wrap gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-800">
            Pusat <span class="italic inline-block pr-1.5 bg-gradient-to-r from-stat-orange to-amber-400 bg-clip-text text-transparent">Edukasi</span>
            &amp; <span class="italic text-[#A7B178]">Nutrisi</span>
        </h1>
        <p class="text-sm text-gray-400 mt-1">Panduan olahraga, resep makanan sehat &amp; artikel kesehatan.</p>
    </div>
    <div class="relative">
        <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
        <input type="text" id="searchInput" placeholder="Cari konten" onkeyup="filterKonten()" class="pl-10 pr-4 py-2.5 rounded-full border border-brand-border text-sm w-64 focus:outline-none focus:border-brand">
    </div>
</div>

@foreach (['video_olahraga' => 'Video olahraga', 'video_resep' => 'Video resep makanan sehat', 'artikel' => 'Artikel kesehatan'] as $tipe => $label)
    <div class="mb-8 kategori-section" data-tipe="{{ $tipe }}">
        <h2 class="font-bold text-gray-800 mb-3">{{ $label }}</h2>
        @if (isset($kontenPerTipe[$tipe]) && $kontenPerTipe[$tipe]->count())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($kontenPerTipe[$tipe] as $konten)
                    <button type="button"
                    data-judul="{{ $konten->judul }}"
                    data-deskripsi="{{ $konten->deskripsi }}"
                    data-teks="{{ $konten->konten_teks }}"
                    data-youtube="{{ $konten->url_video_youtube }}"
                    data-tanggal="{{ $konten->tanggal_publish ? \Carbon\Carbon::parse($konten->tanggal_publish)->translatedFormat('d F Y') : '' }}"
                    onclick="bukaKontenElement(this)"
                    class="konten-card flex flex-col text-left bg-white rounded-2xl overflow-hidden border border-brand-border hover:border-brand transition-all duration-300 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-brand/10">
            <div class="h-36 bg-gray-100 relative group shrink-0">
            
            {{-- AWAL LOGIKA THUMBNAIL DINAMIS --}}
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
                <img src="{{ $ytThumbnail }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="{{ $konten->judul }}">
            @elseif ($konten->thumbnail)
                <img src="{{ asset('storage/' . $konten->thumbnail) }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="{{ $konten->judul }}">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs font-bold">No Image</div>
            @endif
            {{-- AKHIR LOGIKA THUMBNAIL DINAMIS --}}

        @if ($konten->url_video_youtube)
            <div class="absolute inset-0 flex items-center justify-center bg-black/10 group-hover:bg-black/20 transition-colors">
                <div class="w-9 h-9 rounded-full bg-white/90 flex items-center justify-center transition-transform duration-300 group-hover:scale-110">
                    <svg class="w-4 h-4 text-brand ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
            </div>
        @endif
        </div>
        <div class="p-3.5 border-t border-brand-border flex flex-col justify-between flex-1">
            <div>
                <p class="font-semibold text-xs text-gray-800 mb-1 line-clamp-1">{{ $konten->judul }}</p>
                <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed">{{ Str::limit($konten->deskripsi ?: 'Deskripsi', 60) }}</p>
            </div>
            @if ($konten->tanggal_publish)
                <div class="flex items-center gap-1.5 text-[11px] text-gray-400 mt-2.5 pt-2 border-t border-gray-100">
                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Dipublikasikan: {{ \Carbon\Carbon::parse($konten->tanggal_publish)->translatedFormat('d M Y') }}</span>
                </div>
            @endif
        </div>
        </button>
                @endforeach
            </div>
            <div class="no-result-msg hidden bg-white border border-dashed border-brand-border rounded-2xl p-6 text-center text-sm text-gray-400 mt-2">
                Konten tidak ditemukan.
            </div>
        @else
            <div class="bg-white border border-dashed border-brand-border rounded-2xl p-6 text-center text-sm text-gray-400">
                Belum ada konten di kategori ini.
            </div>
        @endif
    </div>
@endforeach

<!-- Modal -->
<div id="kontenModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center p-4 z-50">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between p-5 border-b border-brand-border">
            <div>
                <h3 id="modalJudul" class="font-bold text-base text-gray-800"></h3>
                <p id="modalTanggal" class="text-xs text-gray-400 mt-1 flex items-center gap-1.5"></p>
            </div>
            <button onclick="tutupModal()" class="text-gray-400 hover:text-gray-700 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5">
            <div id="modalVideoWrap" class="hidden mb-4">
                <iframe id="modalVideo" class="w-full aspect-video rounded-xl" src="" allowfullscreen></iframe>
            </div>
            <p id="modalDeskripsi" class="text-sm text-gray-600 leading-relaxed whitespace-pre-line"></p>
        </div>
    </div>
</div>

<script>
// Fungsi Pencarian Real-Time
function filterKonten() {
    const query = document.getElementById('searchInput').value.toLowerCase().trim();
    const sections = document.querySelectorAll('.kategori-section');

    sections.forEach(section => {
        const cards = section.querySelectorAll('.konten-card');
        const noResultMsg = section.querySelector('.no-result-msg');
        let visibleCount = 0;

        cards.forEach(card => {
            const judul = (card.getAttribute('data-judul') || '').toLowerCase();
            const deskripsi = (card.getAttribute('data-deskripsi') || '').toLowerCase();
            const teks = (card.getAttribute('data-teks') || '').toLowerCase();

            if (judul.includes(query) || deskripsi.includes(query) || teks.includes(query)) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (noResultMsg) {
            if (visibleCount === 0 && cards.length > 0) {
                noResultMsg.classList.remove('hidden');
            } else {
                noResultMsg.classList.add('hidden');
            }
        }
    });
}

function extractYoutubeId(url) {
    if (!url) return null;
    const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    return match ? match[1] : null;
}

function bukaKontenElement(el) {
    const judul = el.getAttribute('data-judul');
    const deskripsi = el.getAttribute('data-deskripsi');
    const teks = el.getAttribute('data-teks');
    const youtubeUrl = el.getAttribute('data-youtube');
    const tanggal = el.getAttribute('data-tanggal');

    document.getElementById('modalJudul').textContent = judul || '';
    
    const modalTanggal = document.getElementById('modalTanggal');
    if (tanggal) {
        modalTanggal.innerHTML = `<svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> <span>Dipublikasikan: ${tanggal}</span>`;
        modalTanggal.classList.remove('hidden');
    } else {
        modalTanggal.classList.add('hidden');
    }
    
    const videoWrap = document.getElementById('modalVideoWrap');
    const videoFrame = document.getElementById('modalVideo');
    
    if (youtubeUrl) {
        const ytId = extractYoutubeId(youtubeUrl);
        if (ytId) { 
            videoFrame.src = 'https://www.youtube.com/embed/' + ytId + '?autoplay=1'; 
            videoWrap.classList.remove('hidden'); 
        } else {
            videoWrap.classList.add('hidden'); 
            videoFrame.src = '';
        }
    } else { 
        videoWrap.classList.add('hidden'); 
        videoFrame.src = ''; 
    }
    
    document.getElementById('modalDeskripsi').textContent = teks || deskripsi || 'Tidak ada deskripsi.';
    document.getElementById('kontenModal').classList.remove('hidden');
    document.getElementById('kontenModal').classList.add('flex');
}

function tutupModal() {
    document.getElementById('kontenModal').classList.add('hidden');
    document.getElementById('kontenModal').classList.remove('flex');
    document.getElementById('modalVideo').src = '';
}

document.getElementById('kontenModal').addEventListener('click', function(e) { 
    if (e.target === this) tutupModal(); 
});
</script>
@endsection
