@extends('layouts.admin')

@section('title', 'Laporan & Rekapitulasi')
@section('subtitle', 'Rekap aktivitas seluruh pengguna per bulan')

@section('content')

{{-- Filter Bulan & Tahun --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <select name="bulan" class="bg-white border border-brand-border rounded-xl px-3 py-2 text-sm text-gray-700 font-medium">
        @foreach (range(1, 12) as $b)
            <option value="{{ $b }}" {{ $b == $bulan ? 'selected' : '' }}>
                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
            </option>
        @endforeach
    </select>
    <select name="tahun" class="bg-white border border-brand-border rounded-xl px-3 py-2 text-sm text-gray-700 font-medium">
        @foreach (range(now()->year, now()->year - 2) as $t)
            <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
        Filter
    </button>
</form>

{{-- Kartu Ringkasan --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-5 mb-8">
    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Sesi</span>
        <h2 class="text-2xl font-extrabold text-brand mt-2">{{ number_format($totalSesi) }}</h2>
        <p class="text-[11px] text-gray-400 mt-1">Sesi olahraga bulan ini</p>
    </div>
    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Jarak</span>
        <h2 class="text-2xl font-extrabold text-brand-purple mt-2">{{ number_format($totalJarak, 1) }} km</h2>
        <p class="text-[11px] text-gray-400 mt-1">Jarak tempuh semua pengguna</p>
    </div>
    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Kalori</span>
        <h2 class="text-2xl font-extrabold text-brand-warm mt-2">{{ number_format($totalKalori) }} kkal</h2>
        <p class="text-[11px] text-gray-400 mt-1">Kalori terbakar semua pengguna</p>
    </div>
    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Langkah</span>
        <h2 class="text-2xl font-extrabold text-brand-rose mt-2">{{ number_format($totalLangkah) }}</h2>
        <p class="text-[11px] text-gray-400 mt-1">Langkah semua pengguna</p>
    </div>
</div>

{{-- Tabel Rekap Per Pengguna --}}
<div class="bg-white border border-brand-border rounded-3xl overflow-hidden shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-brand-border">
        <h2 class="text-sm font-extrabold text-gray-800">
            Rekap Per Pengguna —
            <span class="text-brand font-bold">
                {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
            </span>
        </h2>
        <span class="text-xs font-bold text-gray-400">{{ $rekapList->count() }} Pengguna</span>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-brand-border text-left text-gray-400 text-xs uppercase tracking-wider">
                <th class="px-6 py-3 font-bold">Nama Pengguna</th>
                <th class="px-6 py-3 font-bold">Sesi</th>
                <th class="px-6 py-3 font-bold">Total Jarak</th>
                <th class="px-6 py-3 font-bold">Total Kalori</th>
                <th class="px-6 py-3 font-bold">Total Langkah</th>
                <th class="px-6 py-3 font-bold">Rata-rata Berat</th>
                <th class="px-6 py-3 font-bold text-center">Detail</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekapList as $rekap)
            <tr class="border-b border-brand-border last:border-0 hover:bg-brand-bg transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-brand/10 text-brand flex items-center justify-center text-xs font-extrabold shrink-0">
                            {{ strtoupper(substr($rekap->akun->nama_lengkap ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-xs leading-tight">{{ $rekap->akun->nama_lengkap ?? '-' }}</p>
                            <p class="text-[10px] text-gray-400">{{ $rekap->akun->email ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand/10 text-brand">
                        {{ $rekap->total_sesi }} sesi
                    </span>
                </td>
                <td class="px-6 py-4 font-medium text-brand-purple">{{ number_format($rekap->total_jarak_km, 1) }} km</td>
                <td class="px-6 py-4 font-medium text-brand-warm">{{ number_format($rekap->total_kalori_terbakar) }} kkal</td>
                <td class="px-6 py-4 font-medium text-gray-600">{{ number_format($rekap->total_langkah) }}</td>
                <td class="px-6 py-4 font-medium text-gray-600">
                    {{ $rekap->rata_rata_berat_badan ? $rekap->rata_rata_berat_badan . ' kg' : '-' }}
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="{{ route('admin.rekapan.show', [$rekap->akun->id_akun, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-brand-bg border border-brand-border text-xs font-bold text-brand hover:bg-brand hover:text-white hover:border-brand transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-3 text-gray-400">
                        <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-9 0h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-semibold">Belum ada aktivitas di bulan ini.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
