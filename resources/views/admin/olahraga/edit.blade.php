@extends('layouts.admin')

@section('title', 'Edit Jadwal Olahraga')
@section('subtitle', 'Perbarui detail jadwal yang sudah dibagikan')

@section('content')
<div class="max-w-lg bg-brand-card border border-brand-soft rounded-3xl p-6">

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs">
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.olahraga.jadwal.update', $jadwal->id_jadwal) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Pilih Pengguna</label>
            <select name="id_akun" required class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                @foreach ($akunList as $akun)
                    <option value="{{ $akun->id_akun }}" {{ $jadwal->id_akun == $akun->id_akun ? 'selected' : '' }}>
                        {{ $akun->nama_lengkap }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Jenis Olahraga</label>
            <select name="id_jenis" required class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                @foreach ($jenisList as $jenis)
                    <option value="{{ $jenis->id_jenis }}" {{ $jadwal->id_jenis == $jenis->id_jenis ? 'selected' : '' }}>
                        {{ $jenis->nama_jenis }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Tugas / Catatan</label>
            <input type="text" name="tugas" value="{{ old('tugas', $jadwal->tugas) }}" required
                class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
        </div>
        <div>
            <label class="block text-xs font-semibold text-brand-deep mb-1.5">Tanggal Tugas</label>
            <input type="date" name="tanggal_tugas" value="{{ old('tanggal_tugas', \Carbon\Carbon::parse($jadwal->tanggal_tugas)->format('Y-m-d')) }}" required
                min="{{ date('Y-m-d') }}"
                class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
        </div>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 py-3 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.olahraga.jadwal') }}" class="py-3 px-5 border border-brand-soft text-brand-deep font-bold text-sm rounded-xl hover:bg-brand-bg transition text-center">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection