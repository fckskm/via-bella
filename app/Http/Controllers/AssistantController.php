<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

/**
 * Демонстрационный консультант: сценарий на правилах и подборе туров из БД.
 * Это НЕ искусственный интеллект и не подключён к внешним моделям.
 */
class AssistantController extends Controller
{
    private const RULES = [
        'sea' => ['море', 'пляж', 'побереж', 'солнце', 'sea', 'жар'],
        'mountains' => ['гор', 'альп', 'озер', 'озёр', 'природ', 'поход'],
        'culture' => ['музе', 'истори', 'культур', 'архитект', 'город'],
        'gastro' => ['еда', 'вино', 'гастро', 'кухн', 'винодел', 'ресторан'],
        'wellness' => ['спа', 'релакс', 'отдых', 'тишин', 'wellness', 'восстанов'],
        'signature' => ['необычн', 'маршрут', 'авторск', 'поезд'],
    ];

    public function reply(Request $request)
    {
        $text = mb_strtolower(trim((string) $request->validate(['message' => ['required', 'string', 'max:300']])['message']));

        $categories = collect(self::RULES)->filter(fn ($words) => collect($words)->contains(fn ($w) => str_contains($text, $w)))->keys();
        $country = collect(config('via.countries'))->search(fn ($c, $code) => str_contains($text, mb_strtolower($c['name'])) || str_contains($text, mb_strtolower(mb_substr($c['name'], 0, 5))));
        $budget = preg_match('/(\d[\d\s]{2,6})/u', $text, $m) ? (int) preg_replace('/\s+/', '', $m[1]) : null;

        if (preg_match('/привет|здравств|добрый/u', $text) && $categories->isEmpty() && ! $country) {
            return response()->json(['text' => 'Здравствуйте! Расскажите, какое путешествие вы представляете: море, горы, города или гастрономия? Можно указать страну и бюджет на человека.', 'tours' => []]);
        }
        if ($categories->isEmpty() && ! $country && ! $budget) {
            return response()->json(['text' => 'Я пока умею подбирать поездки по настроению. Напишите, например: «хочу море и вино в Италии до 3000».', 'tours' => []]);
        }

        $q = Tour::published()->withRating();
        if ($categories->isNotEmpty()) {
            $q->whereIn('category', $categories);
        }
        if ($country !== false && $country !== null) {
            $q->where('country', $country);
        }
        if ($budget && $budget > 300) {
            $q->where('price', '<=', $budget);
        }
        $tours = $q->orderByDesc('rating_avg')->limit(3)->get();

        if ($tours->isEmpty()) {
            return response()->json(['text' => 'По этим условиям я ничего не нашла. Попробуйте увеличить бюджет или выбрать другую страну — или откройте каталог.', 'tours' => []]);
        }

        return response()->json([
            'text' => 'Вот что подойдёт под ваше настроение. Это подбор по правилам каталога — финальное решение, конечно, за вами.',
            'tours' => $tours->map(fn ($t) => [
                'title' => $t->title, 'meta' => $t->countryName() . ' · ' . $t->durationText() . ' · ' . eur($t->price),
                'url' => route('tours.show', $t), 'image' => $t->coverUrl(),
            ]),
        ]);
    }
}
