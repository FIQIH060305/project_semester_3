<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Obsess</title>
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
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand to-brand-dark mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-brand-deep">Buat Akun Baru</h1>
            <p class="text-sm text-brand-dark/60 mt-1">Langkah 1 dari 2 — Isi data dasar dulu</p>
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

        <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                    placeholder="Nama kamu"
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-4 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand">
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="nama@email.com"
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-4 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand">
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Password</label>
                <input type="password" name="password" required
                    placeholder="Minimal 8 karakter"
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-4 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand">
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    placeholder="Ulangi password"
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-4 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand">
            </div>
            <button type="submit"
                class="w-full py-3 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
                Lanjut Isi Profil
            </button>
        </form>

        <p class="text-center text-xs text-brand-dark/60 mt-6">
            Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-brand-dark">Masuk di sini</a>
        </p>
    </div>
</body>
</html>