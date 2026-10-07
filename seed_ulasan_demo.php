<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = \App\Models\Admin::first();
if (! $admin) {
    throw new RuntimeException('Admin not found.');
}

$items = [
    [
        'id_admin' => $admin->id_admin,
        'tipe_konten' => 'artikel',
        'judul' => 'Tips Jalan Kaki 30 Menit untuk Kesehatan Jantung',
        'deskripsi' => 'Panduan mudah untuk mulai rutin jalan kaki setiap hari.',
        'konten_teks' => 'Jalan kaki 30 menit dapat membantu menjaga detak jantung tetap stabil dan meningkatkan kebugaran tubuh.',
        'kategori' => 'kesehatan',
        'tanggal_publish' => now()->subDays(2)->toDateString(),
    ],
    [
        'id_admin' => $admin->id_admin,
        'tipe_konten' => 'video_olahraga',
        'judul' => 'Workout Ringan di Rumah 15 Menit',
        'deskripsi' => 'Video latihan ringan untuk pemula agar tubuh lebih bugar.',
        'url_video_youtube' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'kategori' => 'latihan',
        'tanggal_publish' => now()->subDay()->toDateString(),
    ],
    [
        'id_admin' => $admin->id_admin,
        'tipe_konten' => 'video_resep',
        'judul' => 'Resep Smoothie Buah untuk Energi Pagi',
        'deskripsi' => 'Cara membuat smoothie sehat dan segar di pagi hari.',
        'url_video_youtube' => 'https://www.youtube.com/watch?v=ysz5S6PUM-U',
        'kategori' => 'nutrisi',
        'tanggal_publish' => now()->toDateString(),
    ],
];

foreach ($items as $item) {
    \App\Models\KontenEdukasi::updateOrCreate(
        ['judul' => $item['judul']],
        $item
    );
}

$user = \App\Models\Akun::first();
if ($user) {
    $konten = \App\Models\KontenEdukasi::query()->first();
    if ($konten) {
        \App\Models\UlasanRating::updateOrCreate(
            ['id_akun' => $user->id_akun, 'id_konten' => $konten->id_konten],
            ['rating' => 5, 'ulasan' => 'Kontennya sangat membantu dan mudah dipahami.']
        );
    }
}

echo "Demo konten dan ulasan berhasil dibuat.\n";
