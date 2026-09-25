@extends('layouts.app-user')
@section('title', 'Rekapan Aktivitas')

@section('content')
@php
    // Fallback jika variabel $bulan / $tahun tidak dikirim dari Controller
    $selectedBulan = $bulan ?? request('bulan', date('m'));
    $selectedTahun = $tahun ?? request('tahun', date('Y'));
@endphp

<div class="flex items-center justify-between mb-6 flex-wrap gap-4">
    <h1 class="text-2xl font-extrabold text-gray-800">
        Lihat semua <span class="bg-gradient-to-r from-stat-orange to-amber-400 bg-clip-text text-transparent">progres</span> anda
    </h1>
    <form method="GET" class="flex gap-2">
        <select name="bulan" onchange="this.form.submit()" class="border border-brand-border rounded-full px-4 py-2 text-sm">
            @foreach (range(1, 12) as $b)
                <option value="{{ $b }}" {{ $b == $selectedBulan ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
        <select name="tahun" onchange="this.form.submit()" class="border border-brand-border rounded-full px-4 py-2 text-sm">
            @foreach (range(now()->year, now()->year - 2) as $t)
                <option value="{{ $t }}" {{ $t == $selectedTahun ? 'selected' : '' }}>
                    Tahun {{ $t }}
                </option>
            @endforeach
        </select>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-stat-greenBg rounded-2xl p-5">
        <span class="text-xs font-bold text-gray-600">TOTAL LANGKAH</span>
        <p class="text-2xl font-extrabold text-gray-800 mt-2">{{ number_format($totalLangkah ?? 0) }}</p>
    </div>
    <div class="bg-stat-roseBg rounded-2xl p-5">
        <span class="text-xs font-bold text-gray-600">TOTAL JARAK</span>
        <p class="text-2xl font-extrabold text-gray-800 mt-2">{{ number_format($totalJarak ?? 0, 2) }} <span class="text-sm font-normal">/ KM</span></p>
    </div>
    <div class="bg-stat-amberBg rounded-2xl p-5">
        <span class="text-xs font-bold text-gray-600">TOTAL KALORI TERBAKAR</span>
        <p class="text-2xl font-extrabold text-gray-800 mt-2">{{ number_format($totalKalori ?? 0) }} <span class="text-sm font-normal">/ Kcal</span></p>
    </div>
</div>

<div class="bg-white border border-brand-border rounded-3xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800">Riwayat bulan ini</h3>
        <button onclick="window.print()" class="flex items-center gap-2 bg-brand hover:bg-brand-dark text-white text-xs font-bold px-4 py-2 rounded-xl">
            Unduh riwayat
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        </button>
    </div>
    @forelse ($riwayatBulanIni ?? [] as $r)
        <div class="flex items-center justify-between py-3 border-b border-brand-border last:border-0">
            <div>
                <p class="text-sm font-bold text-gray-800">{{ $r->jarak_km }} km &middot; {{ $r->jumlah_langkah }} langkah</p>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($r->tanggal_waktu)->translatedFormat('d M Y, H:i') }}</p>
            </div>
            <span class="text-xs font-bold bg-stat-amberBg text-stat-amber px-3 py-1 rounded-full">{{ $r->kalori_terbakar }} kkal</span>
        </div>
    @empty
        <p class="text-sm text-gray-400 text-center py-8">Belum ada aktifitas bulan ini</p>
    @endforelse
</div>
@endsection