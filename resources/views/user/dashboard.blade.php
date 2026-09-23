<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna - Obsess</title>
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
    @if ($notifikasiBelumDibaca->count())
    <div class="space-y-3 mb-6">
        @foreach ($notifikasiBelumDibaca as $notif)
            <div class="flex items-center justify-between bg-brand-warm/10 border border-brand-warm/30 rounded-2xl p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-brand-warm shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-sm text-brand-deep">{{ $notif->pesan }}</p>
                </div>
                <form action="{{ route('user.notifikasi.read', $notif->id_notifikasi) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-brand hover:text-brand-dark shrink-0">Tandai dibaca</button>
                </form>
            </div>
        @endforeach
    </div>
@endif
    <div class="max-w-6xl mx-auto p-6">
        <div class="bg-gradient-to-r from-brand to-brand-dark rounded-3xl p-8 text-white mb-6">
            <span class="inline-block bg-white/20 text-xs font-semibold px-3 py-1 rounded-full mb-3">
                {{ $target ? 'Target Aktif Hari Ini' : 'Belum Ada Target' }}
            </span>
            <h1 class="text-2xl font-bold mb-2">Semangat, {{ explode(' ', $akun->nama_lengkap)[0] }}! Raih Target Sehatmu.</h1>
            <p class="text-white/80 text-sm">
                @if ($target)
                    Target beratmu: {{ $target->berat_target }} kg. Tetap konsisten latihan dan pola makan sehat!
                @else
                    Kamu belum punya target aktif.
                    <a href="{{ route('user.profile.edit') }}" class="underline font-semibold">Atur target sekarang</a>.
                @endif
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-brand-card border border-brand-soft rounded-2xl p-5">
                <p class="text-xs font-semibold text-brand-dark/70 mb-2">LANGKAH HARIAN</p>
                <p class="text-2xl font-extrabold">{{ number_format($totalLangkahHariIni) }}
                    <span class="text-sm font-normal text-brand-dark/50">/ {{ $target->target_langkah_harian ?? '-' }}</span>
                </p>
            </div>
            <div class="bg-brand-card border border-brand-soft rounded-2xl p-5">
                <p class="text-xs font-semibold text-brand-dark/70 mb-2">JARAK TEMPUH</p>
                <p class="text-2xl font-extrabold">{{ number_format($totalJarakHariIni, 1) }}
                    <span class="text-sm font-normal text-brand-dark/50">/ {{ $target->target_jarak_harian ?? '-' }} km</span>
                </p>
            </div>
            <div class="bg-brand-card border border-brand-soft rounded-2xl p-5">
                <p class="text-xs font-semibold text-brand-warm mb-2">KALORI TERBAKAR</p>
                <p class="text-2xl font-extrabold">{{ number_format($totalKaloriHariIni) }}
                    <span class="text-sm font-normal text-brand-dark/50">/ {{ $target->target_kalori_harian ?? '-' }} kkal</span>
                </p>
            </div>
            <div class="bg-brand-card border border-brand-soft rounded-2xl p-5">
                <p class="text-xs font-semibold text-brand-dark/70 mb-2">BERAT & BMI</p>
                @if ($beratTerakhir)
                    <p class="text-2xl font-extrabold">{{ $beratTerakhir->berat_badan }} <span class="text-sm font-normal text-brand-dark/50">kg (BMI {{ $beratTerakhir->bmi }})</span></p>
                @else
                    <p class="text-sm text-brand-dark/50">Belum ada data</p>
                @endif
            </div>
        </div>

        <div class="bg-brand-card border border-brand-soft rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-brand-deep">Riwayat Aktivitas Terakhir</h3>
                <a href="{{ route('user.rekap.index') }}" class="text-sm font-semibold text-brand hover:text-brand-dark">Lihat Semua Rekap &rarr;</a>
            </div>
            @forelse ($akun->riwayatOlahraga()->latest('tanggal_waktu')->take(5)->get() as $riwayat)
                <div class="flex items-center justify-between py-3 border-b border-brand-soft/50 last:border-0">
                    <div>
                        <p class="text-sm font-semibold">{{ $riwayat->jarak_km }} km</p>
                        <p class="text-xs text-brand-dark/50">{{ \Carbon\Carbon::parse($riwayat->tanggal_waktu)->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <span class="text-xs font-semibold bg-brand-bg text-brand-dark px-3 py-1 rounded-full">{{ $riwayat->kalori_terbakar }} kkal</span>
                </div>
            @empty
                <p class="text-sm text-brand-dark/50 text-center py-6">Belum ada riwayat olahraga.</p>
            @endforelse
        </div>
    </div>
</body>
</html>