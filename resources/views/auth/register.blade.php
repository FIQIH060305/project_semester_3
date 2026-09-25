<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - GERAK</title>
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
                border: '#E5EAF3'
              }
            }
          }
        }
      }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-brand-bg min-h-screen flex items-center justify-center p-4">

    <!-- Container Card (Kompa, max-w-sm & p-6) -->
    <div class="w-full max-w-sm bg-white border border-brand-border rounded-2xl p-6 shadow-lg shadow-brand/5">

        <!-- Header / Logo diperbesar ke h-16 -->
        <div class="text-center mb-5">
            <img src="{{ asset('assets/images/logo.png') }}" alt="GERAK" class="h-20 w-auto object-contain mx-auto mb-2">
            <h1 class="text-xl font-bold text-gray-800">Daftar akun baru</h1>
            <p class="text-xs text-gray-400 mt-0.5">Langkah 1 dari 2 — Isi data dasar kamu</p>
        </div>

        <!-- Alert Error -->
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200">
                <ul class="text-xs text-red-600 space-y-1 list-disc pl-4 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Register -->
        <form action="{{ route('register.submit') }}" method="POST" class="space-y-3.5">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                    placeholder="Masukkan nama lengkap"
                    class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-brand focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="nama@email.com"
                    class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-brand focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    placeholder="Minimal 8 karakter"
                    class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-brand focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    placeholder="Ulangi password"
                    class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-brand focus:bg-white transition">
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl shadow-md shadow-brand/20 transition duration-200 mt-1">
                Lanjut Isi Profil
            </button>
        </form>

        <!-- Footer Link -->
        <p class="text-center text-xs text-gray-400 mt-5 font-medium">
            Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-brand hover:underline">Masuk di sini</a>
        </p>
    </div>

</body>
</html>