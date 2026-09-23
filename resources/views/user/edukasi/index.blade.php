<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konten Edukasi - Obsess</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: { colors: { brand: {
        bg: '#F5F9FE', card: '#FFFFFF', soft: '#B5D4F4',
        DEFAULT: '#378ADD', dark: '#185FA5', deep: '#042C53',
        accent: '#85B7EB', warm: '#F0A93B',
      }}}}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-brand-bg text-brand-deep min-h-screen">
    @include('partials.navbar-user')

    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-1">Pusat Edukasi & Nutrisi</h1>
        <p class="text-sm text-brand-dark/60 mb-6">Panduan olahraga, resep sehat, dan artikel dari admin.</p>

        @forelse (['video_olahraga' => 'Video Olahraga', 'video_resep' => 'Resep Makanan Sehat', 'artikel' => 'Artikel & Edukasi'] as $tipe => $label)
            <div class="mb-8">
                <h2 class="font-bold text-brand-deep mb-3">{{ $label }}</h2>
                @if (isset($kontenPerTipe[$tipe]) && $kontenPerTipe[$tipe]->count())
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ($kontenPerTipe[$tipe] as $konten)
                            <button type="button"
                                onclick="bukaKonten({
                                    tipe: '{{ $konten->tipe_konten }}',
                                    judul: @js($konten->judul),
                                    deskripsi: @js($konten->deskripsi ?? ''),
                                    kontenTeks: @js($konten->konten_teks ?? ''),
                                    youtubeUrl: @js($konten->url_video_youtube ?? '')
                                })"
                                class="text-left bg-brand-card border border-brand-soft rounded-2xl overflow-hidden hover:border-brand transition">
                                <div class="h-32 bg-brand-soft/40 flex items-center justify-center relative">
                                    @if ($konten->thumbnail)
                                        <img src="{{ asset('storage/' . $konten->thumbnail) }}"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                             class="w-full h-full object-cover">
                                    @endif
                                    <div class="w-full h-full items-center justify-center absolute inset-0 {{ $konten->thumbnail ? 'hidden' : 'flex' }}">
                                        @if ($tipe === 'artikel')
                                            <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2zM9 10h6M9 14h6" />
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            </svg>
                                        @endif
                                    </div>
                                    @if ($konten->url_video_youtube)
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                            <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-brand ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <div class="p-4">
                                @if ($konten->kategori)
                                    <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-accent/20 text-brand-dark mb-1.5">
                                {{ $konten->kategori }}
                                    </span>
                                 @endif
                                        <h4 class="text-sm font-semibold mb-1">{{ $konten->judul }}</h4>
                                        <p class="text-xs text-brand-dark/60">{{ Str::limit($konten->deskripsi, 60) }}</p>
                                        </div>
                                    <h4 class="text-sm font-semibold mb-1">{{ $konten->judul }}</h4>
                                    <p class="text-xs text-brand-dark/60">{{ Str::limit($konten->deskripsi, 60) }}</p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="bg-brand-card border border-dashed border-brand-soft rounded-2xl p-6 text-center text-sm text-brand-dark/50">
                        Belum ada konten di kategori ini.
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Modal pemutar video / artikel — tetap di halaman ini, tidak redirect ke YouTube -->
    <div id="kontenModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center p-4 z-50">
        <div class="bg-brand-card rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-brand-soft">
                <h3 id="modalJudul" class="font-bold text-brand-deep"></h3>
                <button onclick="tutupModal()" class="text-brand-dark/50 hover:text-brand-deep">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <div id="modalVideoWrap" class="hidden mb-4">
                    <iframe id="modalVideo" class="w-full aspect-video rounded-xl" src="" allowfullscreen></iframe>
                </div>
                <p id="modalDeskripsi" class="text-sm text-brand-dark/70 leading-relaxed"></p>
            </div>
        </div>
    </div>

    <script>
        function extractYoutubeId(url) {
            const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            return match ? match[1] : null;
        }

        function bukaKonten(data) {
            document.getElementById('modalJudul').textContent = data.judul;

            const videoWrap = document.getElementById('modalVideoWrap');
            const videoFrame = document.getElementById('modalVideo');

            if (data.youtubeUrl) {
                const ytId = extractYoutubeId(data.youtubeUrl);
                if (ytId) {
                    videoFrame.src = 'https://www.youtube.com/embed/' + ytId + '?autoplay=1';
                    videoWrap.classList.remove('hidden');
                } else {
                    videoWrap.classList.add('hidden');
                }
            } else {
                videoWrap.classList.add('hidden');
                videoFrame.src = '';
            }

            document.getElementById('modalDeskripsi').textContent = data.kontenTeks || data.deskripsi || 'Tidak ada deskripsi.';
            document.getElementById('kontenModal').classList.remove('hidden');
            document.getElementById('kontenModal').classList.add('flex');
        }

        function tutupModal() {
            document.getElementById('kontenModal').classList.add('hidden');
            document.getElementById('kontenModal').classList.remove('flex');
            document.getElementById('modalVideo').src = ''; // stop video saat modal ditutup
        }

        // Tutup modal kalau klik area gelap di luar kotak
        document.getElementById('kontenModal').addEventListener('click', function (e) {
            if (e.target === this) tutupModal();
        });
    </script>
</body>
</html>