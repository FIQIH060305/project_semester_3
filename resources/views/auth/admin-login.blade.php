<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - GERAK</title>
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

    <!-- Container Card (Kompak max-w-sm & p-6) -->
    <div class="w-full max-w-sm bg-white border border-brand-border rounded-2xl p-6 shadow-lg shadow-brand/5 relative">

        <!-- Badge Panel Admin -->
        <div class="flex justify-end mb-1">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand/10 text-brand border border-brand/20">
                <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                Panel Admin
            </span>
        </div>

        <!-- Header / Logo -->
        <div class="text-center mb-5">
            <img src="{{ asset('assets/images/logo.png') }}" alt="GERAK" class="h-20 w-auto object-contain mx-auto mb-2">
            <h1 class="text-xl font-bold text-gray-800">Login Sebagai Admin</h1>
            <p class="text-xs text-gray-400 mt-0.5">Kelola konten, jadwal, dan rekap data</p>
        </div>

        <!-- Alert Error -->
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-xs text-red-600 font-medium leading-tight">{{ $errors->first() }}</p>
            </div>
        @endif

        <!-- Form Login Admin -->
        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-3.5">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="Masukkan email"
                    class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-brand focus:bg-white transition"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="Masukkan password"
                    class="w-full bg-brand-bg border border-brand-border rounded-xl px-3.5 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-brand focus:bg-white transition"
                >
            </div>

            <button 
                type="submit"
                class="w-full py-2.5 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl shadow-md shadow-brand/20 transition duration-200 flex items-center justify-center gap-2 mt-1"
            >
                <span>Masuk Dashboard Admin</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </form>

    </div>

</body>
</html>