<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class Antropometri
{
    // Batas IMT normal dewasa (Pedoman Depkes/Kemenkes & WHO)
    public const IMT_NORMAL_MIN = 18.5;
    public const IMT_NORMAL_MAX = 25.0;

    public static function imt(float $beratKg, float $tinggiCm): float
    {
        $m = $tinggiCm / 100;
        return round($beratKg / ($m * $m), 1);
    }

    // Berat terendah yang masih IMT >= 18,5 (dibulatkan ke atas, 1 desimal)
    public static function beratNormalMin(float $tinggiCm): float
    {
        $m = $tinggiCm / 100;
        return ceil(self::IMT_NORMAL_MIN * $m * $m * 10) / 10;
    }

    // Berat tertinggi yang masih IMT <= 25,0 (dibulatkan ke bawah, 1 desimal)
    public static function beratNormalMax(float $tinggiCm): float
    {
        $m = $tinggiCm / 100;
        return floor(self::IMT_NORMAL_MAX * $m * $m * 10) / 10;
    }

    public static function format(float $angka): string
    {
        return number_format($angka, 1, ',', '.');
    }

    public static function pastikanTargetWajar(float $tinggiCm, float $beratKg, float $targetKg, string $namaField): void
    {
        $min = self::beratNormalMin($tinggiCm);
        $max = self::beratNormalMax($tinggiCm);

        // Berat sekarang sudah di batas bawah normal atau lebih rendah: menurunkan berat tidak relevan
        if ($beratKg <= $min) {
            throw ValidationException::withMessages([
                $namaField => 'IMT kamu saat ini ' . self::format(self::imt($beratKg, $tinggiCm))
                    . ' sudah berada di batas bawah normal (18,5). Aplikasi ini untuk penurunan berat badan, '
                    . 'jadi target tidak dapat ditetapkan. Silakan konsultasi dengan tenaga kesehatan.',
            ]);
        }

        // Target membuat IMT jatuh ke kategori kurus
        if ($targetKg < $min) {
            throw ValidationException::withMessages([
                $namaField => 'Target ' . self::format($targetKg) . ' kg terlalu rendah untuk tinggi '
                    . self::format($tinggiCm) . ' cm (IMT di bawah 18,5 termasuk kategori kurus). '
                    . 'Berat badan normal untuk tinggimu berkisar ' . self::format($min) . ' sampai '
                    . self::format($max) . ' kg (IMT 18,5 sampai 25,0).',
            ]);
        }
    }
}