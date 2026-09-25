@extends('layouts.app-user')
@section('title', 'Profile')

@section('content')
@if (session('success'))
    <div class="mb-6 p-3.5 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<div class="max-w-lg mx-auto">
    <div class="bg-white border border-brand-border rounded-3xl p-8 text-center mb-4">
        <div class="w-20 h-20 rounded-full bg-brand text-white flex items-center justify-center text-2xl font-extrabold mx-auto mb-4">
            {{ strtoupper(substr($akun->nama_lengkap, 0, 2)) }}
        </div>
        <h1 class="text-lg font-extrabold text-gray-800">{{ $akun->nama_lengkap }}</h1>
        <p class="text-sm text-gray-400">{{ $akun->email }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="bg-stat-greenBg rounded-2xl p-5 text-center">
            <p class="text-xs font-bold text-gray-600 mb-1">TINGGI BADAN</p>
            <p class="text-xl font-extrabold text-gray-800">{{ $akun->tinggi_badan }} cm</p>
        </div>
        <div class="bg-stat-amberBg rounded-2xl p-5 text-center">
            <p class="text-xs font-bold text-gray-600 mb-1">TARGET BERAT</p>
            <p class="text-xl font-extrabold text-gray-800">{{ $target->berat_target ?? '-' }} kg</p>
        </div>
    </div>

    <a href="{{ route('user.profile.edit') }}"
       class="block text-center w-full py-3.5 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition mb-3">
        Edit Profil &amp; Target
    </a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full py-3.5 border border-red-200 text-red-500 hover:bg-red-50 font-bold text-sm rounded-xl transition">
            Logout
        </button>
    </form>
</div>
@endsection