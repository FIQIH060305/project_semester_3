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

    <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-4" novalidate>
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tinggi Badan (cm)</label>
            <input type="number" step="0.1" name="tinggi_badan" id="tinggiInput"
                value="{{ old('tinggi_badan', $akun->tinggi_badan) }}" min="140" max="200"
                class="w-full border border-brand-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
            <p class="text-xs text-gray-400 mt-1">Rentang yang diterima: 140 sampai 200 cm.</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Berat Badan Sekarang (kg)</label>
            <input type="number" step="0.1" name="berat_badan" value="{{ old('berat_badan') }}"
                placeholder="Timbang dulu, lalu isi di sini" min="50" max="200"
                class="w-full border border-brand-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
            <p class="text-xs text-gray-400 mt-1">Setiap kamu isi ini, otomatis tercatat sebagai riwayat baru dan IMT dihitung ulang.</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Target Berat Badan (kg)</label>
            <input type="number" step="0.1" name="berat_target"
                value="{{ old('berat_target', $target->berat_target ?? '') }}"
                class="w-full border border-brand-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand">
            <p id="hintTarget" class="text-xs text-brand mt-1"></p>
        </div>
        <button type="submit" class="w-full py-3.5 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
            Simpan Perubahan
        </button>
    </form>
</div>

<script>
    const tinggiInput = document.getElementById('tinggiInput');
    const hint = document.getElementById('hintTarget');

    function updateHint() {
        const cm = parseFloat(tinggiInput.value);
        if (!cm || cm < 140 || cm > 200) {
            hint.textContent = 'Isi tinggi badan (140 sampai 200 cm) untuk melihat kisaran berat badan normal.';
            return;
        }
        const m = cm / 100;
        const min = Math.ceil(18.5 * m * m * 10) / 10;
        const max = Math.floor(25 * m * m * 10) / 10;
        const f = n => n.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
        hint.textContent = 'Kisaran berat badan normal untuk tinggi ' + cm + ' cm: ' + f(min) + ' sampai ' + f(max) + ' kg (IMT 18,5 sampai 25,0, standar Kemenkes).';
    }

    tinggiInput.addEventListener('input', updateHint);
    updateHint();
</script>
@endsection