@extends('layouts.admin')

@section('title', 'Laporan & Rekapitulasi')
@section('subtitle', 'Rekap aktivitas seluruh pengguna per bulan')

@section('content')

<form method="GET" class="flex gap-3 mb-6">
    <select name="bulan" class="bg-brand-bg border border-brand-soft rounded-xl px-3 py-2 text-sm text-brand-deep">
        @foreach (range(1, 12) as $b)
            <option value="{{ $b }}" {{ $b == $bulan ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
            </option>
        @endforeach
    </select>
    <select name="tahun" class="bg-brand-bg border border-brand-soft rounded-xl px-3 py-2 text-sm text-brand-deep">
        @foreach (range(now()->year, now()->year - 2) as $t)
            <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
        Filter
    </button>
</form>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-brand-card border border-brand-soft p-5 rounded-2xl">
        <span class="text-xs font-semibold text-brand-dark/60 uppercase">Total Sesi</span>
        <h2 class="text-2xl font-extrabold text-brand-deep mt-2">{{ number_format($totalSesi) }}</h2>
    </div>
    <div class="bg-brand-card border border-brand-soft p-5 rounded-2xl">
        <span class="text-xs font-semibold text-brand-dark/60 uppercase">Total Jarak</span>
        <h2 class="text-2xl font-extrabold text-brand-purple mt-2">{{ number_format($totalJarak, 1) }} km</h2>
    </div>
    <div class="bg-brand-card border border-brand-soft p-5 rounded-2xl">
        <span class="text-xs font-semibold text-brand-dark/60 uppercase">Total Kalori</span>
        <h2 class="text-2xl font-extrabold text-brand-warm mt-2">{{ number_format($totalKalori) }} kkal</h2>
    </div>
</div>

<div class="bg-brand-card border border-brand-soft rounded-3xl overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-brand-soft text-left text-brand-dark/60 text-xs uppercase">
        <th class="p-4">Nama Pengguna</th>
        <th class="p-4">Total Jarak</th>
        <th class="p-4">Total Kalori</th>
        <th class="p-4">Total Langkah</th>
        <th class="p-4">Rata-rata Berat</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($rekapList as $rekap)
        <tr class="border-b border-brand-soft/60 last:border-0">
            <td class="p-4 font-medium text-brand-deep">{{ $rekap->akun->nama_lengkap ?? '-' }}</td>
            <td class="p-4 text-brand-dark/60">{{ number_format($rekap->total_jarak_km, 1) }} km</td>
            <td class="p-4 text-brand-dark/60">{{ number_format($rekap->total_kalori_terbakar) }} kkal</td>
            <td class="p-4 text-brand-dark/60">{{ number_format($rekap->total_langkah) }}</td>
            <td class="p-4 text-brand-dark/60">{{ $rekap->rata_rata_berat_badan ? $rekap->rata_rata_berat_badan . ' kg' : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="p-8 text-center text-brand-dark/40">Belum ada aktivitas di bulan ini.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection