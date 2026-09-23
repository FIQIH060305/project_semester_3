<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengguna - Obsess</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: { colors: { brand: {
        bg: '#F5F9FE', card: '#FFFFFF', soft: '#B5D4F4',
        DEFAULT: '#378ADD', dark: '#185FA5', deep: '#042C53',
        accent: '#85B7EB', warm: '#F0A93B',
      }}}}}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-brand-bg text-brand-deep min-h-screen flex items-center justify-center p-4 relative overflow-y-auto py-8"">

    <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-soft/60 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-brand-accent/50 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 bg-brand-card border border-brand-soft rounded-3xl p-8 shadow-2xl shadow-brand-dark/10">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand to-brand-dark mb-4 shadow-lg shadow-brand/20">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-brand-deep">Selamat Datang</h1>
            <p class="text-sm text-brand-dark/70 mt-1">Portal web pengguna</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-3.5 rounded-2xl bg-green-50 border border-green-200">
                <p class="text-xs text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-3.5 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-xs text-red-600 leading-relaxed">{{ $errors->first() }}</p>
            </div>
        @endif

        <div class="mb-6 p-3.5 rounded-2xl bg-brand-bg border border-brand-soft flex items-start gap-3">
            <svg class="w-5 h-5 text-brand shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xs text-brand-dark leading-relaxed">
                Belum punya akun? Registrasi pengguna baru dilakukan melalui
                <span class="font-semibold text-brand-dark underline">Aplikasi Mobile</span>.
            </p>
        </div>

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-brand-deep tracking-wider mb-2">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-dark/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="Masukkan email"
                        class="w-full bg-brand-bg border border-brand-soft rounded-xl pl-11 pr-4 py-3 text-sm text-brand-deep placeholder-brand-dark/40 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-brand-deep tracking-wider">Password</label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-dark/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input type="password" name="password" required
                        placeholder="Masukkan password"
                        class="w-full bg-brand-bg border border-brand-soft rounded-xl pl-11 pr-4 py-3 text-sm text-brand-deep placeholder-brand-dark/40 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition">
                </div>
            </div>

            <button type="submit"
                class="w-full py-3.5 px-4 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl shadow-lg shadow-brand/25 transition duration-200 flex items-center justify-center gap-2">
                <span>Masuk ke Beranda</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>
    </div>
</body>
</html>