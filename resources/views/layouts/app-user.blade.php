<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'GERAK')</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { theme: { extend: { colors: {
    brand: { DEFAULT: '#2F6FED', dark: '#1E56C8', bg: '#F5F8FC', card: '#FFFFFF', border: '#E5EAF3' },
    stat: { green: '#22C55E', greenBg: '#D9F7E4', rose: '#EF4444', roseBg: '#FFE1E1', amber: '#D97706', amberBg: '#FFF1CC', orange: '#FF7A29' },
  }}}}
</script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-brand-bg">

<nav class="bg-white border-b-2 border-brand px-6 py-3">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        <!-- Logo diperbesar dari h-12 menjadi h-16 (bisa diganti h-20 jika ingin lebih besar lagi) -->
        <img src="{{ asset('assets/images/logo.png') }}" alt="GERAK" class="h-20 w-auto object-contain">
        
        <div class="hidden md:flex items-center gap-2">
            <a href="{{ route('user.dashboard') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-bold transition {{ request()->routeIs('user.dashboard') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-brand-bg' }}">
                Beranda
            </a>
            <a href="{{ route('user.edukasi.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-bold transition {{ request()->routeIs('user.edukasi.index') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-brand-bg' }}">
                Konten edukasi
            </a>
            <a href="{{ route('user.rekap.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-bold transition {{ request()->routeIs('user.rekap.index') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-brand-bg' }}">
                Rekapan aktifitas
            </a>
            <a href="{{ route('user.profile.show') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-bold transition {{ request()->routeIs('user.profile.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-brand-bg' }}">
                Profile
            </a>
        </div>
        <button class="md:hidden" onclick="document.getElementById('mobileNav').classList.toggle('hidden')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
    <div id="mobileNav" class="hidden md:hidden mt-3 flex flex-col gap-1">
        <a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request()->routeIs('user.dashboard') ? 'bg-brand text-white' : 'text-gray-700' }}">Beranda</a>
        <a href="{{ route('user.edukasi.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request()->routeIs('user.edukasi.index') ? 'bg-brand text-white' : 'text-gray-700' }}">Konten edukasi</a>
        <a href="{{ route('user.rekap.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request()->routeIs('user.rekap.index') ? 'bg-brand text-white' : 'text-gray-700' }}">Rekapan aktifitas</a>
        <a href="{{ route('user.profile.show') }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request()->routeIs('user.profile.*') ? 'bg-brand text-white' : 'text-gray-700' }}">Profile</a>
    </div>
</nav>

<main class="max-w-6xl mx-auto p-6">
    @yield('content')
</main>

</body>
</html>