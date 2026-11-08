<?php

namespace App\Http\Controllers;

use App\Models\Tour;

class DestinationController extends Controller
{
    public function index()
    {
        return view('destinations.index', ['counts' => Tour::published()->selectRaw('country, count(*) c')->groupBy('country')->pluck('c', 'country')]);
    }

    public function show(string $country)
    {
        $data = config("via.countries.$country");
        abort_unless($data, 404);

        return view('destinations.show', [
            'code' => $country,
            'country' => $data,
            'tours' => Tour::published()->withRating()->with('guide')->where('country', $country)->get(),
            'hotels' => collect(config('via.hotels'))->where('country', $country),
        ]);
    }

    public function map()
    {
        $points = collect(config('via.map_points'))->map(function ($p) {
            $p['tours'] = Tour::published()->where('country', $p['country'])->get()
                ->filter(fn ($t) => $this->near($t, $p))->map(fn ($t) => [
                    'title' => $t->title, 'url' => route('tours.show', $t), 'price' => (int) $t->price, 'duration' => $t->durationText(), 'kind' => $t->kind,
                ])->values();
            $p['country_name'] = config("via.countries.{$p['country']}.name");
            $p['img'] = img($p['img']);

            return $p;
        });

        return view('destinations.map', ['points' => $points]);
    }

    private function near(Tour $t, array $p): bool
    {
        if (! $t->lat) {
            return false;
        }

        return abs($t->lat - $p['lat']) < 1.2 && abs($t->lng - $p['lng']) < 1.6;
    }
}
