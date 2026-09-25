@extends('layouts.app-user')
@section('title', 'Edit Profile')

@section('content')
<div class="max-w-lg mx-auto bg-white border border-brand-border rounded-3xl p-8">
    <h1 class="text-lg font-extrabold text-gray-800 mb-1">Edit Profil &amp; Target</h1>
    <p class="text-sm text-gray-400 mb-6">Perbarui data ini setiap kamu menimbang berat badan.</p>

    @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-2xl bg-red-50 border border-red-200">
            <ul class="text-xs text-red-600 space-y-1 list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tinggi Badan (cm)</label>
            <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $akun->tinggi_badan) }}"
                min="140" max="200" maxlength="3"
                class="w-full border border-brand-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Berat Badan Sekarang (kg)</label>
            <input type="number" name="berat_badan" placeholder="Timbang dulu, lalu isi di sini" required
                min="30" max="250" maxlength="3"
                class="w-full border border-brand-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
            <p class="text-xs text-gray-400 mt-1">Setiap kamu isi ini, otomatis tercatat sebagai riwayat baru dan BMI dihitung ulang.</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Target Berat Badan (kg)</label>
            <input type="number" name="berat_target" value="{{ old('berat_target', $target->berat_target ?? '') }}"
                min="30" max="250" maxlength="3"
                class="w-full border border-brand-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
        </div>
        <button type="submit" class="w-full py-3.5 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection