<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Support\DemoCatalog;
use Illuminate\Http\Request;

/** Демонстрационные разделы: авиабилеты, отели, конструктор. Реальных броней и оплат нет. */
class PlannerController extends Controller
{
    public function flights(Request $request)
    {
        $f = $request->validate([
            'from' => ['nullable', 'string', 'max:40'], 'country' => ['nullable', 'string', 'max:30'],
            'date' => ['nullable', 'date'], 'guests' => ['nullable', 'integer', 'min:1', 'max:9'],
        ]);
        $results = isset($f['country']) ? DemoCatalog::flights($f['from'] ?? 'Москва', $f['country'], $f['date'] ?? now()->addMonth()->toDateString(), (int) ($f['guests'] ?? 1)) : [];

        return view('planner.flights', ['filters' => $f, 'results' => $results]);
    }

    public function hotels(Request $request)
    {
        $country = $request->query('country');
        $hotels = collect(config('via.hotels'))->when($country, fn ($c) => $c->where('country', $country))->values();

        return view('planner.hotels', ['hotels' => $hotels, 'country' => $country]);
    }

    public function builder()
    {
        return view('planner.builder', ['countries' => config('via.countries')]);
    }

    /** Данные для конструктора: рейсы/отели — демо, экскурсии — реальные из БД. */
    public function builderOptions(Request $request)
    {
        $c = $request->validate(['country' => ['required', 'string', 'in:' . implode(',', array_keys(config('via.countries')))], 'from' => ['nullable', 'string', 'max:40'], 'date' => ['nullable', 'date']]);

        return response()->json([
            'flights' => DemoCatalog::flights($c['from'] ?? 'Москва', $c['country'], $c['date'] ?? now()->addMonth()->toDateString(), 1),
            'hotels' => collect(config('via.hotels'))->where('country', $c['country'])->values()->map(fn ($h) => $h + ['image' => img($h['img'])]),
            'tours' => Tour::published()->where('country', $c['country'])->get()->map(fn ($t) => [
                'slug' => $t->slug, 'title' => $t->title, 'price' => (int) $t->price, 'duration' => $t->durationText(), 'kind' => $t->kind, 'image' => $t->coverUrl(), 'url' => route('tours.show', $t),
            ]),
        ]);
    }
}
