<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - GERAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: {
                DEFAULT: '#2F6FED',
                dark: '#1E56C8',
                bg: '#F5F8FC',
                card: '#FFFFFF',
                border: '#E5EAF3',
                purple: '#8B5CF6',
                warm: '#F59E0B',
                rose: '#EF4444'
              }
            }
          }
        }
      }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-brand-bg text-gray-800 antialiased min-h-screen flex overflow-x-hidden">

    <!-- SIDEBAR NAVIGASI KIRI (Collapsible) -->
    <aside id="sidebar" class="sidebar-transition w-64 bg-white border-r border-brand-border flex flex-col justify-between shrink-0 relative z-30 min-h-screen">
        <div>
            <!-- Header Sidebar, Logo & Toggle Button -->
            <div class="p-5 flex items-center justify-between border-b border-brand-border h-20">
                <div class="flex items-center gap-3 sidebar-text overflow-hidden whitespace-nowrap">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="GERAK" class="h-10 w-auto object-contain">
                    <div>
                        <p class="font-extrabold text-sm text-gray-800 leading-none">GERAK</p>
                        <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Portal admin</p>
                    </div>
                </div>
                <!-- Tombol Toggle Buka / Tutup -->
                <button onclick="toggleSidebar()" class="p-2 rounded-xl bg-brand-bg hover:bg-brand-border text-gray-600 transition shrink-0" title="Buka/Tutup Sidebar">
                    <svg id="toggleIcon" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
            </div>

            <!-- Menu Navigasi Utama -->
            <nav class="p-4 space-y-1.5">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-brand text-white shadow-md shadow-brand/20' : 'text-gray-600 hover:bg-brand-bg hover:text-brand' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                    </svg>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                <!-- Kelola Akun -->
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request()->routeIs('admin.users.*') ? 'bg-brand text-white shadow-md shadow-brand/20' : 'text-gray-600 hover:bg-brand-bg hover:text-brand' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-8.13a4 4 0 110 8 4 4 0 010-8zm6 4a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                    <span class="sidebar-text">Kelola Akun Pengguna</span>
                </a>

                <!-- Kelola Konten -->
                <a href="{{ route('admin.konten.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request()->routeIs('admin.konten.*') ? 'bg-brand text-white shadow-md shadow-brand/20' : 'text-gray-600 hover:bg-brand-bg hover:text-brand' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    <span class="sidebar-text">Kelola Konten Edukasi</span>
                </a>

                <!-- Kelola Olahraga -->
                <a href="{{ route('admin.olahraga.jadwal') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request()->routeIs('admin.olahraga.*') ? 'bg-brand text-white shadow-md shadow-brand/20' : 'text-gray-600 hover:bg-brand-bg hover:text-brand' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="sidebar-text">Kelola Jadwal Olahraga</span>
                </a>

                <!-- Rekapitulasi -->
                <a href="{{ route('admin.rekapan.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request()->routeIs('admin.rekapan.*') ? 'bg-brand text-white shadow-md shadow-brand/20' : 'text-gray-600 hover:bg-brand-bg hover:text-brand' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-9 0h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="sidebar-text">Kelola Rekapan Aktifitas</span>
                </a>
            </nav>
        </div>

        <!-- Tombol Logout -->
        <div class="p-4 border-t border-brand-border">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-red-500 hover:bg-red-50 transition whitespace-nowrap">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="sidebar-text">Log out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- AREA KONTEN UTAMA -->
    <main class="flex-1 flex flex-col min-w-0">
        <!-- Topbar Header -->
        <header class="bg-white border-b border-brand-border px-8 py-4 h-20 flex items-center justify-between shrink-0">
            <div>
                <h1 class="text-lg font-extrabold text-gray-800 leading-tight">@yield('title', 'Dashboard')</h1>
                <p class="text-xs text-gray-400 mt-0.5">@yield('subtitle', 'Ringkasan aktivitas sistem')</p>
            </div>

            <!-- Profile Badge Admin -->
            <div class="flex items-center gap-3 bg-brand-bg border border-brand-border rounded-2xl px-3.5 py-1.5">
                <div class="w-8 h-8 rounded-xl bg-brand text-white flex items-center justify-center text-xs font-extrabold shadow-sm">
                    {{ strtoupper(substr(auth('admin')->user()->nama ?? 'A', 0, 2)) }}
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold text-gray-800 leading-tight">{{ auth('admin')->user()->nama ?? 'Admin GERAK' }}</p>
                    <p class="text-[10px] text-brand font-semibold">Administrator</p>
                </div>
            </div>
        </header>

        <!-- Area Isi Halaman (Dinamis dari View) -->
        <div class="p-8 flex-1 overflow-y-auto">
            @yield('content')
        </div>
    </main>

    <!-- Script Buka / Tutup Sidebar -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const texts = document.querySelectorAll('.sidebar-text');
            const icon = document.getElementById('toggleIcon');

            if (sidebar.classList.contains('w-64')) {
                // Ciutkan Sidebar (Collapse)
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                texts.forEach(t => t.classList.add('hidden'));
                icon.style.transform = 'rotate(180deg)';
            } else {
                // Perluas Sidebar (Expand)
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                texts.forEach(t => t.classList.remove('hidden'));
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>
</html>