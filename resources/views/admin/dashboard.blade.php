@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('subtitle', 'Ringkasan seluruh aktivitas pengguna dan konten edukasi')

@section('content')

<!-- Grid Statistik Utama -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pengguna</span>
        <h2 class="text-3xl font-extrabold text-brand mt-2">{{ number_format($totalPengguna) }}</h2>
        <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ number_format($totalAkunAktif) }} Akun Aktif</p>
    </div>

    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Konten Edukasi</span>
        <h2 class="text-3xl font-extrabold text-brand-purple mt-2">{{ number_format($totalKonten) }}</h2>
        <p class="text-[11px] text-gray-400 mt-2 font-medium">
            Video ({{ $kontenPerTipe['video_olahraga'] ?? 0 }}),
            Resep ({{ $kontenPerTipe['video_resep'] ?? 0 }}),
            Artikel ({{ $kontenPerTipe['artikel'] ?? 0 }})
        </p>
    </div>

    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal Olahraga</span>
        <h2 class="text-3xl font-extrabold text-brand-warm mt-2">{{ number_format($totalJadwal) }}</h2>
        <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ $jadwalHariIni }} Jadwal untuk Hari Ini</p>
    </div>

    <div class="bg-white border border-brand-border p-5 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Riwayat Aktivitas</span>
        <h2 class="text-3xl font-extrabold text-brand-rose mt-2">{{ number_format($totalRiwayat) }}</h2>
        <p class="text-[11px] text-gray-400 mt-2 font-medium">Sesi olahraga tersimpan</p>
    </div>

</div>

<!-- Shortcut Navigasi Cepat -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <a href="{{ route('admin.users.index') }}" class="p-6 rounded-2xl bg-white border border-brand-border hover:border-brand hover:shadow-md transition group flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-2xl bg-brand/10 text-brand flex items-center justify-center mb-4 font-bold text-xl">👥</div>
            <h2 class="text-base font-bold text-gray-800 group-hover:text-brand transition">Kelola Akun Pengguna</h2>
            <p class="text-xs text-gray-400 mt-2 leading-relaxed">Lihat profil pengguna terdaftar, serta aktifkan atau blokir akun.</p>
        </div>
        <span class="mt-6 text-xs font-bold text-brand flex items-center gap-1">Buka Manajemen User &rarr;</span>
    </a>

    <a href="{{ route('admin.konten.index') }}" class="p-6 rounded-2xl bg-white border border-brand-border hover:border-brand-purple hover:shadow-md transition group flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-2xl bg-brand-purple/10 text-brand-purple flex items-center justify-center mb-4 font-bold text-xl">📝</div>
            <h2 class="text-base font-bold text-gray-800 group-hover:text-brand-purple transition">Kelola Konten & Edukasi</h2>
            <p class="text-xs text-gray-400 mt-2 leading-relaxed">Tambah materi video YouTube, resep sehat, atau artikel kesehatan.</p>
        </div>
        <span class="mt-6 text-xs font-bold text-brand-purple flex items-center gap-1">Buka Manajemen Konten &rarr;</span>
    </a>

    <a href="{{ route('admin.rekapan.index') }}" class="p-6 rounded-2xl bg-white border border-brand-border hover:border-brand-warm hover:shadow-md transition group flex flex-col justify-between">
        <div>
            <div class="w-12 h-12 rounded-2xl bg-brand-warm/10 text-brand-warm flex items-center justify-center mb-4 font-bold text-xl">📊</div>
            <h2 class="text-base font-bold text-gray-800 group-hover:text-brand-warm transition">Laporan & Rekapitulasi</h2>
            <p class="text-xs text-gray-400 mt-2 leading-relaxed">Lihat rekap aktivitas seluruh pengguna per bulan.</p>
        </div>
        <span class="mt-6 text-xs font-bold text-brand-warm flex items-center gap-1">Buka Rekapan &rarr;</span>
    </a>

</div>
@endsection