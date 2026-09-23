<nav class="bg-brand-card border-b border-brand-soft px-6 py-3">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand to-brand-dark flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span class="font-extrabold text-brand-deep text-sm">ObesitasSistem</span>
        </div>

        <div class="hidden md:flex items-center gap-1">
            <a href="{{ route('user.dashboard') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('user.dashboard') ? 'bg-brand text-white' : 'text-brand-deep hover:bg-brand-bg' }}">
                Beranda
            </a>
            <a href="{{ route('user.edukasi.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('user.edukasi.index') ? 'bg-brand text-white' : 'text-brand-deep hover:bg-brand-bg' }}">
                Konten Edukasi
            </a>
            <a href="{{ route('user.rekap.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('user.rekap.index') ? 'bg-brand text-white' : 'text-brand-deep hover:bg-brand-bg' }}">
                Rekapan Aktivitas
            </a>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('user.profile.show') }}" class="flex items-center gap-2 group">
                <div class="w-9 h-9 rounded-full bg-brand-accent flex items-center justify-center text-xs font-bold text-brand-deep">
                    {{ strtoupper(substr(auth('web')->user()->nama_lengkap ?? 'U', 0, 2)) }}
                </div>
                <span class="text-sm font-semibold text-brand-deep hidden sm:block group-hover:text-brand-dark">
                    {{ auth('web')->user()->nama_lengkap ?? 'Pengguna' }}
                </span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-9 h-9 rounded-full hover:bg-red-50 flex items-center justify-center text-red-500" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</nav>