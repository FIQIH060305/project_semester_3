@extends('layouts.app-user')
@section('title', 'Beranda')

@section('content')

@if ($notifikasiBelumDibaca->count())
    <div class="space-y-2 mb-5">
        @foreach ($notifikasiBelumDibaca as $notif)
            <div class="flex items-center justify-between bg-stat-amberBg border border-stat-amber/30 rounded-2xl p-4">
                <p class="text-sm text-gray-700">{{ $notif->pesan }}</p>
                <form action="{{ route('user.notifikasi.read', $notif->id_notifikasi) }}" method="POST">
                    @csrf
                    <button class="text-xs font-bold text-brand hover:underline shrink-0 ml-3">Tandai dibaca</button>
                </form>
            </div>
        @endforeach
    </div>
@endif

<div class="bg-gradient-to-r from-brand to-brand-dark rounded-3xl p-8 text-white mb-6">
    <span class="inline-block bg-white/20 text-xs font-bold px-3 py-1 rounded-full mb-3">Target aktif hari ini</span>
    <h1 class="text-2xl font-extrabold mb-1">Semangat, {{ explode(' ', $akun->nama_lengkap)[0] }}! Raih Target Sehatmu.</h1>
    <p class="text-white/85 text-sm">
        @if ($target)
            Target beratmu {{ $target->berat_target }}kg. Tetap konsisten latihan dan jaga pola makan sehat.
        @else
            Kamu belum punya target aktif. <a href="{{ route('user.profile.edit') }}" class="underline font-semibold">Atur sekarang</a>.
        @endif
    </p>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-stat-greenBg rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-stat-green" fill="currentColor" viewBox="0 0 24 24"><path d="M13 3a3 3 0 100 6 3 3 0 000-6zM6 21l3-9 4 3 3-6"/></svg>
            <span class="text-xs font-bold text-gray-700">Langkah harian</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">{{ number_format($totalLangkahHariIni) }}</p>
    </div>
    <div class="bg-stat-roseBg rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-stat-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            <span class="text-xs font-bold text-gray-700">Jarak tempuh</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">{{ number_format($totalJarakHariIni, 2) }} <span class="text-sm font-normal">/ KM</span></p>
    </div>
    <div class="bg-stat-amberBg rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-stat-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
            <span class="text-xs font-bold text-gray-700">Kalori terbakar</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">{{ number_format($totalKaloriHariIni) }} <span class="text-sm font-normal">/ Kcal</span></p>
    </div>
    <div class="bg-stat-roseBg rounded-2xl p-5">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-stat-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-3v2m0 16.5V21"/></svg>
            <span class="text-xs font-bold text-gray-700">Berat dan BMI</span>
        </div>
        @if ($beratTerakhir)
            <p class="text-lg font-extrabold text-gray-800">{{ $beratTerakhir->berat_badan }}kg <span class="text-sm font-normal">(BMI {{ $beratTerakhir->bmi }})</span></p>
        @else
            <p class="text-sm font-semibold text-gray-500">Belum ada data</p>
        @endif
    </div>
</div>

<div class="bg-brand-card border border-brand-border rounded-3xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800">Riwayat aktifitas terakhir</h3>
        <a href="{{ route('user.rekap.index') }}" class="text-sm font-bold text-brand hover:underline">Lihat semua rekap</a>
    </div>
    @php $riwayatTerakhir = $akun->riwayatOlahraga()->latest('tanggal_waktu')->take(5)->get(); @endphp
    @forelse ($riwayatTerakhir as $r)
        <div class="flex items-center justify-between py-3 border-b border-brand-border last:border-0">
            <div>
                <p class="text-sm font-bold text-gray-800">{{ $r->jarak_km }} km</p>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($r->tanggal_waktu)->translatedFormat('d M Y, H:i') }}</p>
            </div>
            <span class="text-xs font-bold bg-stat-amberBg text-stat-amber px-3 py-1 rounded-full">{{ $r->kalori_terbakar }} kkal</span>
        </div>
    @empty
        <p class="text-sm text-gray-400 text-center py-8">Belum ada riwayat olahraga</p>
    @endforelse
</div>
@endsection