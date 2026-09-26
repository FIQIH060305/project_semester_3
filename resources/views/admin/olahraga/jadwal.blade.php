@extends('layouts.admin')

@section('title', 'Kelola Olahraga')
@section('subtitle', 'Bagikan jadwal olahraga langsung ke pengguna mobile')

@section('content')
@if (session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-sm font-medium">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="bg-brand-card border border-brand-soft rounded-3xl p-6">
        <h2 class="text-sm font-bold text-brand-deep mb-4">Buat Jadwal Baru</h2>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.olahraga.jadwal.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Pilih Pengguna</label>
                <select name="id_akun" required class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                    <option value="">-- Pilih akun --</option>
                    @foreach ($akunList as $akun)
                        <option value="{{ $akun->id_akun }}">{{ $akun->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Jenis Olahraga</label>
                <select name="id_jenis" required class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
                    <option value="">-- Pilih jenis --</option>
                    @foreach ($jenisList as $jenis)
                        <option value="{{ $jenis->id_jenis }}">{{ $jenis->nama_jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Tugas / Catatan</label>
                <input type="text" name="tugas" placeholder="Contoh: Jalan pagi 30 menit" required
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
            </div>
            <div>
                <label class="block text-xs font-semibold text-brand-deep mb-1.5">Tanggal Tugas</label>
                <input type="date" name="tanggal_tugas" required min="{{ date('Y-m-d') }}"
                    class="w-full bg-brand-bg border border-brand-soft rounded-xl px-3 py-2.5 text-sm text-brand-deep focus:outline-none focus:border-brand">
            </div>
            <button type="submit" class="w-full py-3 bg-brand hover:bg-brand-dark text-white font-bold text-sm rounded-xl transition">
                Bagikan ke Mobile
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 bg-brand-card border border-brand-soft rounded-3xl p-6">
        <h2 class="text-sm font-bold text-brand-deep mb-4">Jadwal Terkirim Terbaru</h2>
        <div class="space-y-3">
            @forelse ($jadwalTerbaru as $jadwal)
                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl border border-brand-soft hover:border-brand/30 hover:shadow-sm transition">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-sm font-semibold text-brand-deep truncate">{{ $jadwal->tugas }}</p>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-warm/10 text-brand-warm shrink-0">
                                {{ $jadwal->jenisOlahraga->nama_jenis ?? '-' }}
                            </span>
                        </div>
                        <p class="text-xs text-brand-dark/50 truncate">
                            Untuk {{ $jadwal->akun->nama_lengkap ?? '-' }} &middot;
                            {{ \Carbon\Carbon::parse($jadwal->tanggal_tugas)->translatedFormat('d M Y') }}
                            &middot;
                            <span class="{{ $jadwal->status === 'selesai' ? 'text-green-600 font-semibold' : '' }}">
                                {{ $jadwal->status === 'selesai' ? 'Selesai' : 'Belum dikerjakan' }}
                            </span>
                        </p>
                    </div>

                    @if ($jadwal->status !== 'selesai')
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('admin.olahraga.jadwal.edit', $jadwal->id_jadwal) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand hover:bg-brand-dark text-white text-xs font-bold shadow-sm shadow-brand/20 transition active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <form id="deleteJadwalForm-{{ $jadwal->id_jadwal }}"
                                  action="{{ route('admin.olahraga.jadwal.destroy', $jadwal->id_jadwal) }}"
                                  method="POST" class="inline">
                                @csrf
                                <button type="button"
                                        onclick="openDeleteModal('{{ $jadwal->id_jadwal }}', '{{ addslashes($jadwal->tugas) }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-xs font-bold shadow-sm shadow-red-500/20 transition active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-brand-dark/40 text-center py-8">Belum ada jadwal yang dibagikan.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl text-center">
        <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h3 class="text-base font-extrabold text-gray-800 mb-2">Hapus Jadwal Ini?</h3>
        <p class="text-sm text-gray-500 mb-6">
            Jadwal <span id="deleteModalTugas" class="font-semibold text-gray-700"></span> akan dihapus dari pengguna dan tidak bisa dikembalikan.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 py-3 rounded-xl border border-brand-border text-gray-600 font-bold text-sm hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="button" onclick="confirmDelete()"
                    class="flex-1 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-sm transition">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
let jadwalIdToDelete = null;

function openDeleteModal(id, tugas) {
    jadwalIdToDelete = id;
    document.getElementById('deleteModalTugas').textContent = '"' + tugas + '"';
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}
function closeDeleteModal() {
    jadwalIdToDelete = null;
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}
function confirmDelete() {
    if (jadwalIdToDelete) {
        document.getElementById('deleteJadwalForm-' + jadwalIdToDelete).submit();
    }
}
document.getElementById('deleteModal').addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endsection