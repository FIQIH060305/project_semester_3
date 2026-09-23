@extends('layouts.admin')

@section('title', 'Kelola Akun Pengguna')
@section('subtitle', 'Aktifkan atau blokir akun pengguna mobile & web')

@section('content')
@if (session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<div class="bg-brand-card border border-brand-soft rounded-3xl overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-brand-soft text-left text-brand-dark/60 text-xs uppercase">
                <th class="p-4">Nama</th>
                <th class="p-4">Email</th>
                <th class="p-4">Usia</th>
                <th class="p-4">Status</th>
                <th class="p-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($akunList as $akun)
                <tr class="border-b border-brand-soft/60 last:border-0">
                    <td class="p-4 font-medium text-brand-deep">{{ $akun->nama_lengkap }}</td>
                    <td class="p-4 text-brand-dark/60">{{ $akun->email }}</td>
                    <td class="p-4 text-brand-dark/60">{{ $akun->usia }} thn</td>
                    <td class="p-4">
                        @if ($akun->status_akun === 'aktif')
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">Blokir</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <form action="{{ route('admin.users.toggle', $akun->id_akun) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-brand-soft text-brand-deep hover:border-brand hover:text-brand transition">
                                {{ $akun->status_akun === 'aktif' ? 'Blokir' : 'Aktifkan' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-8 text-center text-brand-dark/40">Belum ada akun pengguna.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection