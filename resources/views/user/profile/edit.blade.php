<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Obsess</title>
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
<body class="bg-brand-bg text-brand-deep min-h-screen">
    @include('partials.navbar-user')

    <div class="max-w-lg mx-auto p-6">
        <div class="bg-brand-card border border-brand-soft rounded-3xl p-8">
            <h1 class="text-lg font-bold mb-1">Edit Profil & Target</h1>
            <p class="text-sm text-brand-dark/60 mb-6">Perbarui data ini setiap kamu menimbang berat badan.</p>

            @if ($errors->any())
                <div class="mb-6 p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-2">Tinggi Badan (cm)</label>
                    <input type="number" step="0.1" name="tinggi_badan" value="{{ old('tinggi_badan', $akun->tinggi_badan) }}" required
                        class="w-full bg-brand-bg border border-brand-soft rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-2">Berat Badan Sekarang (kg)</label>
                    <input type="number" step="0.1" name="berat_badan" required
                        placeholder="Timbang dulu, lalu isi di sini"
                        class="w-full bg-brand-bg border border-brand-soft rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand">
                    <p class="text-[11px] text-brand-dark/50 mt-1">Setiap kamu isi ini, otomatis tercatat sebagai riwayat baru + BMI dihitung ulang.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-2">Target Berat Badan (kg)</label>
                    <input type="number" step="0.1" name="berat_target" value="{{ old('berat_target', $target->berat_target ?? '') }}" required
                        class="w-full bg-brand-bg border border-brand-soft rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand">
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</body>
</html>