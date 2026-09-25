<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GERAK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = { theme: { extend: { colors: {
        brand: { DEFAULT: '#2F6FED', dark: '#1E56C8' },
        stat: { orange: '#FF7A29' },
      }}}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen relative overflow-x-hidden flex items-center justify-center">

    <!-- Background dengan Auto-Slide & Fallback -->
    <div id="bgContainer" 
         class="fixed inset-0 -z-10 bg-cover bg-center transition-all duration-1000 ease-in-out"
         style="background-image: url('{{ asset('assets/images/backgrounds/' . ($bgImage ?? 'bg1.jpg')) }}');">
    </div>

    <!-- Overlay gelap -->
    <div class="fixed inset-0 -z-10 bg-gradient-to-t from-black/75 via-black/35 to-black/10"></div>

    <!-- Container Utama Centered -->
    <div class="w-full max-w-6xl min-h-screen flex flex-col md:flex-row items-center justify-center px-6 md:px-12 py-10 gap-10 md:gap-20">

        <!-- Headline kiri -->
        <div class="max-w-md text-center md:text-left">
            <h1 class="text-white text-4xl md:text-5xl font-extrabold leading-tight drop-shadow-lg">
                <span class="text-stat-orange">Sehat</span> bermula dengan aktif bergerak.
            </h1>
        </div>

        <!-- Card login -->
        <div class="w-full max-w-xs bg-white border border-gray-100 rounded-2xl p-6 shadow-2xl">
            <div class="text-center mb-5">
                <img src="{{ asset('assets/images/logo.png') }}" alt="GERAK" class="h-25 mx-auto mb-1 drop-shadow-sm">
            </div>

            @if (session('success'))
                <div class="mb-4 p-2.5 rounded-xl bg-green-50 border border-green-200 text-green-700 text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-2.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-600 font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="Masukkan Email"
                        class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3.5 py-2.5 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordInput" required
                            placeholder="Masukkan Password"
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3.5 py-2.5 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent transition">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="text-right">
                    <a href="#" class="text-[11px] text-brand font-semibold hover:underline">Lupa Password?</a>
                </div>
                <button type="submit" class="w-full py-2.5 bg-brand hover:bg-brand-dark text-white font-bold text-xs rounded-lg transition shadow-md shadow-brand/30">
                    Login
                </button>
            </form>

            <p class="text-center text-xs text-gray-600 mt-5">
                Belum punya akun? <a href="{{ route('register') }}" class="text-brand font-bold hover:underline">Daftar</a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        const bgImages = [
            "{{ asset('assets/images/backgrounds/bg-1.jpg') }}",
            "{{ asset('assets/images/backgrounds/bg-2.jpg') }}",
            "{{ asset('assets/images/backgrounds/bg-3.jpg') }}"
        ];

        let currentIndex = 0;
        const bgContainer = document.getElementById('bgContainer');

        function changeBackground() {
            if (bgImages.length > 0) {
                bgContainer.style.backgroundImage = `url('${bgImages[currentIndex]}')`;
                currentIndex = (currentIndex + 1) % bgImages.length;
            }
        }

        setInterval(changeBackground, 5000);
    </script>
</body>
</html>