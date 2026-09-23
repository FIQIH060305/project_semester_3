<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Obsess</title>
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

    <div class="max-w-2xl mx-auto p-6">
        @if (session('success'))
            <div class="mb-6 p-3.5 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-brand-card border border-brand-soft rounded-3xl p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 rounded-full bg-brand-accent flex items-center justify-center text-xl font-bold text-brand-deep">
                    {{ strtoupper(substr($akun->nama_lengkap, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-lg font-bold">{{ $akun->nama_lengkap }}</h1>
                    <p class="text-sm text-brand-dark/60">{{ $akun->email }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-brand-bg rounded-xl p-4">
                    <p class="text-xs text-brand-dark/60 mb-1">Tinggi Badan</p>
                    <p class="text-lg font-bold">{{ $akun->tinggi_badan }} cm</p>
                </div>
                <div class="bg-brand-bg rounded-xl p-4">
                    <p class="text-xs text-brand-dark/60 mb-1">Target Berat</p>
                    <p class="text-lg font-bold">{{ $target->berat_target ?? '-' }} kg</p>
                </div>
            </div>

            <a href="{{ route('user.profile.edit') }}"
               class="block text-center w-full py-3 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
                Edit Profil & Target
            </a>
        </div>
    </div>
</body>
</html>