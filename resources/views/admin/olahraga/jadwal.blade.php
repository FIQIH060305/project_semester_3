@extends('layouts.admin')

@section('title', 'Kelola Olahraga')
@section('subtitle', 'Bagikan jadwal olahraga langsung ke pengguna mobile')

@section('content')
@if (session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="bg-brand-card border border-brand-soft rounded-3xl p-6">
        <h2 class="text-sm font-bold text-brand-deep mb-4">Buat Jadwal Baru</h2>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.olahraga.jadwal.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Pilih Pengguna</label>
                <select name="id_akun" required class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                    <option value="">-- Pilih akun --</option>
                    @foreach ($akunList as $akun)
                        <option value="{{ $akun->id_akun }}">{{ $akun->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Jenis Olahraga</label>
                <select name="id_jenis" required class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                    <option value="">-- Pilih jenis --</option>
                    @foreach ($jenisList as $jenis)
                        <option value="{{ $jenis->id_jenis }}">{{ $jenis->nama_jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Tugas / Catatan</label>
                <input type="text" name="tugas" placeholder="Contoh: Jalan pagi 30 menit" required
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Tanggal Tugas</label>
                <input type="date" name="tanggal_tugas" required
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
            </div>
            <button type="submit" class="w-full py-3 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
                Bagikan ke Mobile
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-brand-card border border-brand-soft rounded-3xl p-6">
        <h2 class="text-sm font-bold text-brand-deep mb-4">Jadwal Terkirim Terbaru</h2>
        <div class="space-y-3">
            @forelse ($jadwalTerbaru as $jadwal)
                <div class="p-4 rounded-2xl border border-brand-soft">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-semibold text-brand-deep">{{ $jadwal->tugas }}</p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-warm/10 text-brand-warm">
                            {{ $jadwal->jenisOlahraga->nama_jenis ?? '-' }}
                        </span>
                    </div>
                    <p class="text-xs text-brand-dark/50">
                        Untuk {{ $jadwal->akun->nama_lengkap ?? '-' }} &middot;
                        {{ \Carbon\Carbon::parse($jadwal->tanggal_tugas)->translatedFormat('d M Y') }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-brand-dark/40 text-center py-8">Belum ada jadwal yang dibagikan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection