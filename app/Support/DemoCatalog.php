<?php

namespace App\Support;

/** Детерминированные демонстрационные рейсы (без внешних API). */
class DemoCatalog
{
    public static function flights(string $from, string $country, string $date, int $guests): array
    {
        $airports = config("via.airports.$country", []);
        $airlines = config('via.airlines');
        $out = [];
        foreach ($airports as $i => $to) {
            for ($k = 0; $k < 2; $k++) {
                $seed = crc32("$from|$to|$date|$k");
                $dep = 6 + ($seed % 14);
                $dur = 3 + ($seed % 3) + ($k ? 1 : 0);
                $price = 140 + ($seed % 320) + $i * 12;
                $out[] = [
                    'id' => md5("$from|$to|$date|$k"),
                    'from' => $from,
                    'to' => $to,
                    'airline' => $airlines[$seed % count($airlines)],
                    'depart' => sprintf('%02d:%02d', $dep, ($seed >> 3) % 4 * 15),
                    'arrive' => sprintf('%02d:%02d', ($dep + $dur) % 24, ($seed >> 3) % 4 * 15),
                    'duration' => $dur . ' ч ' . (($seed >> 5) % 4 * 15) . ' мин',
                    'stops' => $k ? 1 : 0,
                    'price' => $price,
                    'date' => $date,
                ];
            }
        }
        usort($out, fn ($a, $b) => $a['price'] <=> $b['price']);

        return $out;
    }
}
