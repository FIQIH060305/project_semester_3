@extends('layouts.app-user')

@section('title', 'Notifikasi - GERAK')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-extrabold text-gray-800">Notifikasi</h1>
        @if($notifikasiList->where('status_dibaca', false)->count() > 0)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-brand text-white">
                {{ $notifikasiList->where('status_dibaca', false)->count() }} belum dibaca
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse($notifikasiList as $notif)
        <div class="bg-white border {{ $notif->status_dibaca ? 'border-gray-100' : 'border-brand/30 bg-blue-50/30' }} rounded-2xl p-4 flex items-start gap-4 shadow-sm">
            <div class="w-10 h-10 rounded-xl {{ $notif->status_dibaca ? 'bg-gray-100 text-gray-400' : 'bg-brand/10 text-brand' }} flex items-center justify-center shrink-0">
                🔔
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold {{ $notif->status_dibaca ? 'text-gray-500' : 'text-gray-800' }} leading-snug">
                    {{ $notif->pesan }}
                </p>
                @if($notif->jadwal)
                    <p class="text-xs text-gray-400 mt-1">
                        Jadwal: <span class="font-medium">{{ $notif->jadwal->jenisOlahraga->nama_jenis ?? '-' }}</span>
                        — {{ \Carbon\Carbon::parse($notif->jadwal->tanggal_tugas)->translatedFormat('d M Y') }}
                    </p>
                @endif
                <p class="text-[10px] text-gray-400 mt-1">
                    {{ \Carbon\Carbon::parse($notif->waktu_kirim)->diffForHumans() }}
                </p>
            </div>
            @if(!$notif->status_dibaca)
            <form method="POST" action="{{ route('user.notifikasi.read', $notif->id_notifikasi) }}">
                @csrf
                <button type="submit" class="shrink-0 px-3 py-1.5 rounded-xl bg-brand text-white text-xs font-bold hover:bg-brand-dark transition">
                    Tandai dibaca
                </button>
            </form>
            @else
            <span class="shrink-0 px-3 py-1.5 rounded-xl bg-gray-100 text-gray-400 text-xs font-bold">Dibaca</span>
            @endif
        </div>
        @empty
        <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center shadow-sm">
            <p class="text-4xl mb-3">🔕</p>
            <p class="text-sm font-semibold text-gray-500">Belum ada notifikasi.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
