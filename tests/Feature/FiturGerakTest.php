<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Akun;
use App\Models\JadwalOlahraga;
use App\Models\JenisOlahraga;
use App\Models\KontenEdukasi;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiturGerakTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private Akun $user1;

    private Akun $user2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'nama' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->user1 = Akun::create([
            'nama_lengkap' => 'User Satu',
            'email' => 'user1@test.com',
            'password' => bcrypt('password'),
            'jenis_kelamin' => 'L',
            'usia' => 25,
            'tinggi_badan' => 170,
            'berat_badan_awal' => 70,
            'status_akun' => 'aktif',
        ]);

        $this->user2 = Akun::create([
            'nama_lengkap' => 'User Dua',
            'email' => 'user2@test.com',
            'password' => bcrypt('password'),
            'jenis_kelamin' => 'P',
            'usia' => 22,
            'tinggi_badan' => 160,
            'berat_badan_awal' => 55,
            'status_akun' => 'aktif',
        ]);
    }

    public function test_zona_waktu_adalah_asia_jakarta(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
    }

    public function test_halaman_login_tidak_memiliki_link_lupa_password(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertDontSee('Lupa Password?');
    }

    public function test_konten_dengan_tipe_dan_judul_yang_sama_tidak_dapat_diinput_dua_kali(): void
    {
        KontenEdukasi::create([
            'id_admin' => $this->admin->id_admin,
            'tipe_konten' => 'video_olahraga',
            'judul' => 'Senam Pagi',
            'url_video_youtube' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.konten.store'), [
            'tipe_konten' => 'video_olahraga',
            'judul' => 'Senam Pagi',
            'url_video_youtube' => 'https://www.youtube.com/watch?v=abcdefghijk',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $response->assertSessionHasErrors('judul');
    }

    public function test_update_konten_dengan_tipe_dan_judul_yang_sama_dari_konten_lain_ditolak(): void
    {
        $konten1 = KontenEdukasi::create([
            'id_admin' => $this->admin->id_admin,
            'tipe_konten' => 'artikel',
            'judul' => 'Tips Tidur Nyenyak',
            'konten_teks' => 'Artikel tidur.',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $konten2 = KontenEdukasi::create([
            'id_admin' => $this->admin->id_admin,
            'tipe_konten' => 'artikel',
            'judul' => 'Tips Bangun Pagi',
            'konten_teks' => 'Artikel pagi.',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin, 'admin')->put(route('admin.konten.update', $konten2->id_konten), [
            'tipe_konten' => 'artikel',
            'judul' => 'Tips Tidur Nyenyak',
            'konten_teks' => 'Diperbarui.',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $response->assertSessionHasErrors('judul');
    }

    public function test_link_youtube_yang_sama_tetap_ditolak(): void
    {
        KontenEdukasi::create([
            'id_admin' => $this->admin->id_admin,
            'tipe_konten' => 'video_olahraga',
            'judul' => 'Olahraga Kardio',
            'url_video_youtube' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.konten.store'), [
            'tipe_konten' => 'video_resep',
            'judul' => 'Resep Salad Buah',
            'url_video_youtube' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $response->assertSessionHasErrors('url_video_youtube');
    }

    public function test_tanggal_publish_sebelum_hari_ini_ditolak_validasi_server(): void
    {
        $pastDate = today()->subDay()->toDateString();

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.konten.store'), [
            'tipe_konten' => 'artikel',
            'judul' => 'Artikel Manfaat Air Putih',
            'konten_teks' => 'Air putih sangat bermanfaat bagi metabolisme tubuh.',
            'tanggal_publish' => $pastDate,
        ]);

        $response->assertSessionHasErrors('tanggal_publish');
    }

    public function test_tanggal_publish_hari_ini_dan_setelahnya_diterima(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.konten.store'), [
            'tipe_konten' => 'artikel',
            'judul' => 'Artikel Pola Makan Sehat',
            'konten_teks' => 'Konsumsi sayur dan buah yang cukup setiap hari.',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('konten_edukasi', [
            'judul' => 'Artikel Pola Makan Sehat',
        ]);
        $this->assertEquals(today()->toDateString(), KontenEdukasi::where('judul', 'Artikel Pola Makan Sehat')->first()->tanggal_publish->toDateString());

        $futureDate = today()->addDays(3)->toDateString();
        $responseFuture = $this->actingAs($this->admin, 'admin')->post(route('admin.konten.store'), [
            'tipe_konten' => 'artikel',
            'judul' => 'Artikel Jadwal Tidur Sehat',
            'konten_teks' => 'Tidur minimal 7-8 jam per hari untuk menjaga imunitas.',
            'tanggal_publish' => $futureDate,
        ]);

        $responseFuture->assertSessionHasNoErrors();
        $this->assertDatabaseHas('konten_edukasi', [
            'judul' => 'Artikel Jadwal Tidur Sehat',
        ]);
        $this->assertEquals($futureDate, KontenEdukasi::where('judul', 'Artikel Jadwal Tidur Sehat')->first()->tanggal_publish->toDateString());
    }

    public function test_form_tambah_dan_edit_konten_memiliki_field_tanggal_publish_dan_min_attribute(): void
    {
        $today = date('Y-m-d');

        // Form tambah di index admin konten
        $responseIndex = $this->actingAs($this->admin, 'admin')->get(route('admin.konten.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('name="tanggal_publish"', false);
        $responseIndex->assertSee('min="'.$today.'"', false);

        // Form edit konten
        $konten = KontenEdukasi::create([
            'id_admin' => $this->admin->id_admin,
            'tipe_konten' => 'artikel',
            'judul' => 'Artikel Tes Form Edit',
            'konten_teks' => 'Isi artikel.',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $responseEdit = $this->actingAs($this->admin, 'admin')->get(route('admin.konten.edit', $konten->id_konten));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('name="tanggal_publish"', false);
        $responseEdit->assertSee('min="'.$today.'"', false);
    }

    public function test_konten_dengan_tanggal_publish_masa_depan_tidak_ditampilkan_ke_pengguna(): void
    {
        $kontenHariIni = KontenEdukasi::create([
            'id_admin' => $this->admin->id_admin,
            'tipe_konten' => 'artikel',
            'judul' => 'Artikel Hari Ini',
            'konten_teks' => 'Konten ini tayang hari ini.',
            'tanggal_publish' => today()->toDateString(),
        ]);

        $kontenMasaDepan = KontenEdukasi::create([
            'id_admin' => $this->admin->id_admin,
            'tipe_konten' => 'artikel',
            'judul' => 'Artikel Masa Depan Rahasia',
            'konten_teks' => 'Konten ini belum boleh tayang.',
            'tanggal_publish' => today()->addDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($this->user1, 'web')->get(route('user.edukasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Artikel Hari Ini');
        $response->assertSee('Dipublikasikan:');
        $response->assertSee(Carbon::parse(today())->translatedFormat('d M Y'));
        $response->assertSee('Dipublikasikan:');
        $response->assertSee(\Carbon\Carbon::parse(today())->translatedFormat('d M Y'));
        $response->assertDontSee('Artikel Masa Depan Rahasia');
    }

    public function test_navbar_pengguna_menampilkan_tombol_notifikasi_dan_badge(): void
    {
        $jenis = JenisOlahraga::create([
            'nama_jenis' => 'Lari Sore',
            'estimasi_kalori_per_km' => 60.0,
        ]);

        $jadwal = JadwalOlahraga::create([
            'id_akun' => $this->user1->id_akun,
            'id_jenis' => $jenis->id_jenis,
            'id_admin' => $this->admin->id_admin,
            'sumber' => 'admin',
            'tugas' => 'Lari 15 menit',
            'tanggal_tugas' => today()->toDateString(),
            'status' => 'belum',
        ]);

        Notifikasi::create([
            'id_jadwal' => $jadwal->id_jadwal,
            'pesan' => 'Pesan 1',
            'waktu_kirim' => now(),
            'status_dibaca' => false,
        ]);

        Notifikasi::create([
            'id_jadwal' => $jadwal->id_jadwal,
            'pesan' => 'Pesan 2',
            'waktu_kirim' => now(),
            'status_dibaca' => false,
        ]);

        $response = $this->actingAs($this->user1, 'web')->get(route('user.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('🔔');
        $response->assertSee(route('user.notifikasi.index'));
        // Badge jumlah notifikasi yang belum dibaca (2)
        $response->assertSee('2');
    }

    public function test_notifikasi_hanya_dapat_dibaca_oleh_pemilik_jadwal_terkait(): void
    {
        $jenis = JenisOlahraga::create([
            'nama_jenis' => 'Lari Pagi',
            'estimasi_kalori_per_km' => 60.0,
        ]);

        $jadwalUser1 = JadwalOlahraga::create([
            'id_akun' => $this->user1->id_akun,
            'id_jenis' => $jenis->id_jenis,
            'id_admin' => $this->admin->id_admin,
            'sumber' => 'admin',
            'tugas' => 'Lari pagi 30 menit',
            'tanggal_tugas' => today()->toDateString(),
            'status' => 'belum',
        ]);

        $notifikasiUser1 = Notifikasi::create([
            'id_jadwal' => $jadwalUser1->id_jadwal,
            'pesan' => 'Pengingat: Waktunya lari pagi!',
            'waktu_kirim' => now(),
            'status_dibaca' => false,
        ]);

        // User 2 mencoba membaca atau menandai dibaca notifikasi milik User 1 -> 403 Forbidden
        $responseUser2 = $this->actingAs($this->user2, 'web')->post(route('user.notifikasi.read', $notifikasiUser1->id_notifikasi));
        $responseUser2->assertStatus(403);
        $this->assertFalse($notifikasiUser1->fresh()->status_dibaca);

        // User 2 membuka daftar notifikasi -> tidak melihat notifikasi milik User 1
        $responseUser2Index = $this->actingAs($this->user2, 'web')->get(route('user.notifikasi.index'));
        $responseUser2Index->assertStatus(200);
        $responseUser2Index->assertDontSee('Pengingat: Waktunya lari pagi!');

        // User 1 membuka daftar notifikasi -> melihat notifikasi miliknya
        $responseUser1Index = $this->actingAs($this->user1, 'web')->get(route('user.notifikasi.index'));
        $responseUser1Index->assertStatus(200);
        $responseUser1Index->assertSee('Pengingat: Waktunya lari pagi!');

        // User 1 menandai dibaca -> berhasil
        $responseUser1 = $this->actingAs($this->user1, 'web')->post(route('user.notifikasi.read', $notifikasiUser1->id_notifikasi));
        $responseUser1->assertRedirect();
        $this->assertTrue($notifikasiUser1->fresh()->status_dibaca);
    }
}
