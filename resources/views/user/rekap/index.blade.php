<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapan Aktivitas - Obsess</title>
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
        <h1 class="text-2xl font-bold mb-1">Rekapitulasi Aktivitas</h1>
        <p class="text-sm text-brand-dark/60 mb-6">Bulan {{ now()->translatedFormat('F Y') }}</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-brand-card border border-brand-soft rounded-2xl p-5">
                <p class="text-xs font-semibold text-brand-dark/60 mb-2">TOTAL LANGKAH</p>
                <p class="text-2xl font-extrabold">{{ number_format($totalLangkah) }}</p>
            </div>
            <div class="bg-brand-card border border-brand-soft rounded-2xl p-5">
                <p class="text-xs font-semibold text-brand-dark/60 mb-2">TOTAL JARAK</p>
                <p class="text-2xl font-extrabold">{{ number_format($totalJarak, 1) }} <span class="text-sm font-normal">km</span></p>
            </div>
            <div class="bg-brand-card border border-brand-soft rounded-2xl p-5">
                <p class="text-xs font-semibold text-brand-warm mb-2">TOTAL KALORI</p>
                <p class="text-2xl font-extrabold">{{ number_format($totalKalori) }} <span class="text-sm font-normal">kkal</span></p>
            </div>
        </div>

        <div class="bg-brand-card border border-brand-soft rounded-2xl p-6">
            <h3 class="font-bold mb-4">Log Riwayat Bulan Ini</h3>
            @forelse ($riwayatBulanIni as $riwayat)
                <div class="flex items-center justify-between py-3 border-b border-brand-soft/50 last:border-0">
                    <div>
                        <p class="text-sm font-semibold">{{ $riwayat->jarak_km }} km &middot; {{ $riwayat->jumlah_langkah }} langkah</p>
                        <p class="text-xs text-brand-dark/50">{{ \Carbon\Carbon::parse($riwayat->tanggal_waktu)->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <span class="text-xs font-semibold bg-brand-bg text-brand-dark px-3 py-1 rounded-full">{{ $riwayat->kalori_terbakar }} kkal</span>
                </div>
            @empty
                <p class="text-sm text-brand-dark/50 text-center py-6">Belum ada aktivitas bulan ini.</p>
            @endforelse
        </div>
    </div>
</body>
</html>