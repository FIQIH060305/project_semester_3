<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontenEdukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KelolaKontenController extends Controller
{
    public function index()
    {
        $kontenList = KontenEdukasi::latest()->get();
        return view('admin.konten.index', compact('kontenList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe_konten' => ['required', 'in:video_olahraga,video_resep,artikel'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'url_video_youtube' => [
                'nullable',
                'required_if:tipe_konten,video_olahraga,video_resep',
                'url',
                'unique:konten_edukasi,url_video_youtube',
            ],
            'konten_teks' => ['nullable', 'required_if:tipe_konten,artikel', 'string'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ], [
            'tipe_konten.required' => 'Tipe konten wajib dipilih.',
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 150 karakter.',
            'url_video_youtube.required_if' => 'Link YouTube wajib diisi untuk tipe video.',
            'url_video_youtube.url' => 'Format link tidak valid. Pastikan diawali https:// dan berasal dari YouTube.',
            'url_video_youtube.unique' => 'Link YouTube ini sudah dipakai di konten lain. Silakan gunakan video yang berbeda.',
            'konten_teks.required_if' => 'Isi artikel wajib diisi untuk tipe artikel.',
            'thumbnail.image' => 'File yang diunggah harus berupa gambar, bukan dokumen seperti PDF atau Word.',
            'thumbnail.mimes' => 'Format gambar harus JPG, PNG, JPEG.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('konten', 'public');
        }

        $validated['id_admin'] = Auth::guard('admin')->id();

        KontenEdukasi::create($validated);

        return redirect()->route('admin.konten.index')->with('success', 'Konten berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $konten = KontenEdukasi::findOrFail($id);
        return view('admin.konten.edit', compact('konten'));
    }

    public function update(Request $request, $id)
    {
        $konten = KontenEdukasi::findOrFail($id);

        $validated = $request->validate([
            'tipe_konten' => ['required', 'in:video_olahraga,video_resep,artikel'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'url_video_youtube' => [
                'nullable',
                'required_if:tipe_konten,video_olahraga,video_resep',
                'url',
                'unique:konten_edukasi,url_video_youtube,' . $id . ',id_konten',
            ],
            'konten_teks' => ['nullable', 'required_if:tipe_konten,artikel', 'string'],
            'kategori' => ['nullable', 'string', 'max:50'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ], [
            'tipe_konten.required' => 'Tipe konten wajib dipilih.',
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 150 karakter.',
            'url_video_youtube.required_if' => 'Link YouTube wajib diisi untuk tipe video.',
            'url_video_youtube.url' => 'Format link tidak valid. Pastikan diawali https:// dan berasal dari YouTube.',
            'url_video_youtube.unique' => 'Link YouTube ini sudah dipakai di konten lain. Silakan gunakan video yang berbeda.',
            'konten_teks.required_if' => 'Isi artikel wajib diisi untuk tipe artikel.',
            'thumbnail.image' => 'File yang diunggah harus berupa gambar, bukan dokumen seperti PDF atau Word.',
            'thumbnail.mimes' => 'Format gambar harus JPG, PNG, JPEG.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($konten->thumbnail) {
                Storage::disk('public')->delete($konten->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('konten', 'public');
        }

        $konten->update($validated);

        return redirect()->route('admin.konten.index')->with('success', 'Konten berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $konten = KontenEdukasi::findOrFail($id);

        if ($konten->thumbnail) {
            Storage::disk('public')->delete($konten->thumbnail);
        }

        $konten->delete();

        return redirect()->route('admin.konten.index')->with('success', 'Konten berhasil dihapus.');
    }
}