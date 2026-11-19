@extends('layouts.admin')

@section('title', 'Detail Rekap — ' . $akun->nama_lengkap)
@section('subtitle', 'Riwayat aktivitas bulan ' . \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') . ' ' . $tahun)

@section('content')

{{-- Tombol Kembali & Filter --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <a href="{{ route('admin.rekapan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-brand-border text-xs font-bold text-gray-600 hover:bg-brand-bg hover:text-brand transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Rekapan
    </a>

    <form method="GET" class="flex gap-3">
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
</div>

{{-- Profil Pengguna --}}
<div class="bg-white border border-brand-border rounded-3xl p-6 shadow-sm mb-6 flex items-center gap-5">
    <div class="w-14 h-14 rounded-2xl bg-brand/10 text-brand flex items-center justify-center text-xl font-extrabold shrink-0">
        {{ strtoupper(substr($akun->nama_lengkap, 0, 1)) }}
    </div>
    <div>
        <h2 class="text-base font-extrabold text-gray-800">{{ $akun->nama_lengkap }}</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ $akun->email }}</p>
        <p class="text-xs text-gray-400">
            {{ $akun->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }} &bull;
            {{ $akun->usia }} tahun &bull;
            {{ $akun->tinggi_badan }} cm
        </p>
    </div>
    <div class="ml-auto">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
            {{ $akun->status_akun === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
            {{ ucfirst($akun->status_akun) }}
        </span>
    </div>
</div>

{{-- Kartu Ringkasan Rekap Bulan Ini --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-5 mb-6">
    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase">Total Sesi</span>
        <h2 class="text-2xl font-extrabold text-brand mt-2">{{ $riwayatList->count() }}</h2>
    </div>
    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase">Total Jarak</span>
        <h2 class="text-2xl font-extrabold text-brand-purple mt-2">
            {{ $rekap ? number_format($rekap->total_jarak_km, 1) : '0.0' }} km
        </h2>
    </div>
    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase">Total Kalori</span>
        <h2 class="text-2xl font-extrabold text-brand-warm mt-2">
            {{ $rekap ? number_format($rekap->total_kalori_terbakar) : '0' }} kkal
        </h2>
    </div>
    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase">Rata Berat</span>
        <h2 class="text-2xl font-extrabold text-gray-700 mt-2">
            {{ $rekap && $rekap->rata_rata_berat_badan ? $rekap->rata_rata_berat_badan . ' kg' : '-' }}
        </h2>
    </div>
</div>

{{-- Tabel Riwayat Olahraga --}}
<div class="bg-white border border-brand-border rounded-3xl overflow-hidden shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-brand-border">
        <h2 class="text-sm font-extrabold text-gray-800">Riwayat Olahraga</h2>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-brand-border text-left text-gray-400 text-xs uppercase tracking-wider">
                <th class="px-6 py-3 font-bold">Tanggal</th>
                <th class="px-6 py-3 font-bold">Jenis Olahraga</th>
                <th class="px-6 py-3 font-bold">Jarak</th>
                <th class="px-6 py-3 font-bold">Durasi</th>
                <th class="px-6 py-3 font-bold">Langkah</th>
                <th class="px-6 py-3 font-bold">Kalori</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayatList as $riwayat)
            <tr class="border-b border-brand-border last:border-0 hover:bg-brand-bg transition">
                <td class="px-6 py-3 font-medium text-gray-700">
                    {{ \Carbon\Carbon::parse($riwayat->tanggal_waktu)->translatedFormat('d M Y, H:i') }}
                </td>
                <td class="px-6 py-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-purple/10 text-brand-purple">
                        {{ $riwayat->jenisOlahraga->nama_jenis ?? '-' }}
                    </span>
                </td>
                <td class="px-6 py-3 text-brand-purple font-medium">{{ number_format($riwayat->jarak_km, 1) }} km</td>
                <td class="px-6 py-3 text-gray-600">{{ $riwayat->durasi_waktu }} menit</td>
                <td class="px-6 py-3 text-gray-600">{{ number_format($riwayat->jumlah_langkah) }}</td>
                <td class="px-6 py-3 text-brand-warm font-medium">{{ number_format($riwayat->kalori_terbakar) }} kkal</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">
                    Tidak ada riwayat olahraga di bulan ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Tabel Riwayat Berat Badan --}}
@if ($beratList->isNotEmpty())
<div class="bg-white border border-brand-border rounded-3xl overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-brand-border">
        <h2 class="text-sm font-extrabold text-gray-800">Riwayat Berat Badan</h2>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-brand-border text-left text-gray-400 text-xs uppercase tracking-wider">
                <th class="px-6 py-3 font-bold">Tanggal</th>
                <th class="px-6 py-3 font-bold">Berat Badan</th>
                <th class="px-6 py-3 font-bold">BMI</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($beratList as $berat)
            <tr class="border-b border-brand-border last:border-0 hover:bg-brand-bg transition">
                <td class="px-6 py-3 font-medium text-gray-700">
                    {{ \Carbon\Carbon::parse($berat->tanggal_pencatatan)->translatedFormat('d M Y') }}
                </td>
                <td class="px-6 py-3 font-bold text-gray-800">{{ $berat->berat_badan }} kg</td>
                <td class="px-6 py-3 text-gray-600">{{ $berat->bmi ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection

