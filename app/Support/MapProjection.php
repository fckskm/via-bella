<?php

namespace App\Support;

/** Перевод lat/lng в проценты на подложке public/img/europe-map.png (900×570, линейная аппроксимация). */
class MapProjection
{
    public static function xy(float $lat, float $lng): array
    {
        $x = 20.6 * $lng + 228;
        $y = -24.4 * $lat + 1372;

        return [round($x / 900 * 100, 2), round($y / 570 * 100, 2)];
    }
}
