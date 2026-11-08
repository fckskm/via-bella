<?php

if (! function_exists('img')) {
    /** URL фотографии по логическому ключу, например img('places/amalfi'). */
    function img(?string $key, string $fallback = 'places/amalfi'): string
    {
        $key = $key ?: $fallback;
        if (str_starts_with($key, 'http') || str_contains($key, '.')) {
            return str_starts_with($key, 'http') ? $key : asset($key);
        }
        $file = "img/{$key}.jpg";

        return file_exists(public_path($file)) ? asset($file) : asset("img/{$fallback}.jpg");
    }
}

if (! function_exists('eur')) {
    function eur(float|int|string $v): string
    {
        return '€' . number_format((float) $v, 0, ',', "\u{202F}");
    }
}

if (! function_exists('ru_date')) {
    function ru_date($d, string $fmt = 'j F'): string
    {
        return \Carbon\Carbon::parse($d)->locale('ru')->translatedFormat($fmt);
    }
}
