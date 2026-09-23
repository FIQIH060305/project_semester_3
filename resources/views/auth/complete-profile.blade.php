<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil - Obsess</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: { colors: { brand: {
        bg: '#F5F9FE', card: '#FFFFFF', soft: '#B5D4F4',
        DEFAULT: '#378ADD', dark: '#185FA5', deep: '#042C53',
        accent: '#85B7EB', warm: '#F0A93B',
      }}}}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-brand-bg text-brand-deep min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-brand-card border border-brand-soft rounded-3xl p-8 shadow-2xl shadow-brand-dark/10">

        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-brand-deep">Lengkapi Profil Kesehatan</h1>
            <p class="text-sm text-brand-dark/60 mt-1">Langkah 2 dari 2 — Data ini dipakai untuk menghitung BMI</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl bg-red-50 border border-red-200">
                <ul class="text-xs text-red-600 space-y-1 list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('complete-profile.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-brand-deep mb-1.5">Usia</label>
                    <input type="number" name="usia" value="{{ old('usia') }}" required min="5" max="120"
                        placeholder="Tahun"
                        class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-brand-deep mb-1.5">Jenis Kelamin</label>
                    <select name="jenis_kelamin" required
                        class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                        <option value="">Pilih</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-brand-deep mb-1.5">Tinggi Badan (cm)</label>
                    <input type="number" step="0.1" name="tinggi_badan" value="{{ old('tinggi_badan') }}" required min="50" max="250"
                        placeholder="170"
                        class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-brand-deep mb-1.5">Berat Badan (kg)</label>
                    <input type="number" step="0.1" name="berat_badan" value="{{ old('berat_badan') }}" required min="20" max="300"
                        placeholder="85"
                        class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Target Berat Badan (kg)</label>
                <input type="number" step="0.1" name="target_berat" value="{{ old('target_berat') }}" required min="20" max="300"
                    placeholder="70"
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
            </div>
            <button type="submit"
                class="w-full py-3 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
                Simpan dan Selesai
            </button>
        </form>
    </div>
</body>
</html>