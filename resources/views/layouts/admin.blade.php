<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: { colors: {
        brand: {
        bg: '#0B0F17', card: '#161F30', border: '#2A3348',
        DEFAULT: '#2F6FED', dark: '#1E56C8', deep: '#FFFFFF', accent: '#3B82F6', warm: '#FF7A29',
        },
        stat: { green: '#22C55E', greenBg: 'rgba(34,197,94,.12)', rose: '#EF4444', roseBg: 'rgba(239,68,68,.12)', amber: '#D97706', amberBg: 'rgba(217,119,6,.12)' },
        }}}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-brand-bg text-brand-deep min-h-screen relative overflow-x-hidden">

    <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-soft/40 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-1/3 -right-40 w-96 h-96 bg-brand-accent/30 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="flex min-h-screen relative z-10">

        <aside class="w-64 shrink-0 bg-brand-card border-r border-brand-soft flex flex-col">
            <div class="p-6 flex items-center gap-3 border-b border-brand-soft">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand to-brand-dark flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-brand-deep leading-tight">Obsess</p>
                    <p class="text-[11px] text-brand-dark/60">Portal Admin</p>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-brand text-white' : 'text-brand-deep/70 hover:bg-brand-bg' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.users.index') ? 'bg-brand text-white' : 'text-brand-deep/70 hover:bg-brand-bg' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-8.13a4 4 0 110 8 4 4 0 010-8zm6 4a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                    Kelola Akun
                </a>
                <a href="{{ route('admin.konten.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.konten.index') ? 'bg-brand text-white' : 'text-brand-deep/70 hover:bg-brand-bg' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    Kelola Konten
                </a>
                <a href="{{ route('admin.olahraga.jadwal') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.olahraga.jadwal') ? 'bg-brand text-white' : 'text-brand-deep/70 hover:bg-brand-bg' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Kelola Olahraga
                </a>
                <a href="{{ route('admin.rekapan.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.rekapan.index') ? 'bg-brand text-white' : 'text-brand-deep/70 hover:bg-brand-bg' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-9 0h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Rekapitulasi
                </a>
            </nav>

            <div class="p-4 border-t border-brand-soft">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-xl font-bold text-brand-deep">@yield('title', 'Dashboard')</h1>
                    <p class="text-sm text-brand-dark/60 mt-0.5">@yield('subtitle', 'Ringkasan aktivitas sistem')</p>
                </div>
                <div class="flex items-center gap-3 bg-brand-card border border-brand-soft rounded-2xl px-4 py-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand to-brand-dark flex items-center justify-center text-xs font-bold text-white">
                        AD
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-brand-deep leading-tight">{{ auth('admin')->user()->nama ?? 'Admin' }}</p>
                        <p class="text-[10px] text-brand-dark/50">Administrator</p>
                    </div>
                </div>
            </div>

            @yield('content')
        </main>
    </div>

</body>
</html>