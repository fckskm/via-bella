<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Guide;
use App\Models\Tour;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        return view('favorites', [
            'serverKeys' => $request->user() ? $request->user()->favorites()->get()->map(fn ($f) => "{$f->kind}:{$f->ref}")->values() : [],
        ]);
    }

    /** Синхронизация с БД для вошедшего пользователя. */
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'kind' => ['required', 'in:tour,guide,destination,hotel'],
            'ref' => ['required', 'string', 'max:120'],
        ]);
        $existing = Favorite::where('user_id', $request->user()->id)->where($data)->first();
        $existing ? $existing->delete() : Favorite::create($data + ['user_id' => $request->user()->id]);

        return response()->json(['active' => ! $existing]);
    }

    public function merge(Request $request)
    {
        $keys = collect($request->input('keys', []))->filter(fn ($k) => is_string($k) && preg_match('/^(tour|guide|destination|hotel):[\w-]{1,100}$/', $k))->unique()->take(100);
        foreach ($keys as $k) {
            [$kind, $ref] = explode(':', $k, 2);
            Favorite::firstOrCreate(['user_id' => $request->user()->id, 'kind' => $kind, 'ref' => $ref]);
        }

        return response()->json(['keys' => $request->user()->favorites()->get()->map(fn ($f) => "{$f->kind}:{$f->ref}")->values()]);
    }

    /** HTML-карточки для списка избранного (ключи приходят из браузера). */
    public function cards(Request $request)
    {
        $keys = collect(explode(',', (string) $request->query('keys')))->filter()->take(60)
            ->map(fn ($k) => explode(':', $k, 2))->filter(fn ($p) => count($p) === 2);

        $tours = Tour::published()->withRating()->with('guide')->whereIn('slug', $keys->where(0, 'tour')->pluck(1))->get();
        $guides = Guide::withCount('tours')->whereIn('slug', $keys->where(0, 'guide')->pluck(1))->get();
        $hotels = collect(config('via.hotels'))->whereIn('slug', $keys->where(0, 'hotel')->pluck(1));
        $countries = collect(config('via.countries'))->only($keys->where(0, 'destination')->pluck(1)->all());

        return view('partials.favorite-cards', compact('tours', 'guides', 'hotels', 'countries'));
    }
}
