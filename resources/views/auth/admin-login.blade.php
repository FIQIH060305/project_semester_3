<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Obsess</title>
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
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-brand-bg text-brand-deep min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-soft/60 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-brand-accent/50 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 bg-brand-card border border-brand-soft rounded-3xl p-8 shadow-2xl shadow-brand-dark/10">

        <div class="text-right">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-brand-deep/5 text-brand-dark border border-brand-soft">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-dark animate-pulse"></span>
                Panel Admin
            </span>
        </div>

        <div class="text-center mb-8 mt-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-deep to-brand-dark mb-4 shadow-lg shadow-brand-dark/20">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-brand-deep">Login Admin Obsess</h1>
            <p class="text-sm text-brand-dark/70 mt-1">Kelola konten, jadwal, dan rekap data</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-3.5 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-xs text-red-600 leading-relaxed">{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-brand-deep  tracking-wider mb-2">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="masukkan email "
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-4 py-3 text-sm text-brand-deep placeholder-brand-dark/40 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-brand-deep  tracking-wider mb-2">Password Admin</label>
                <input
                    type="password"
                    name="password"
                    required
                    placeholder="Masukkan Password"
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-4 py-3 text-sm text-brand-deep placeholder-brand-dark/40 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition"
                >
            </div>

            <button
                type="submit"
                class="w-full py-3.5 px-4 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl shadow-lg shadow-brand/25 transition duration-200 flex items-center justify-center gap-2"
            >
                <span>Masuk Dashboard Admin</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>
</body>
</html>