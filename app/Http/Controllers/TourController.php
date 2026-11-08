<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /** F-01, F-10: каталог с поиском и фильтрацией. */
    public function index(Request $request)
    {
        $f = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'country' => ['nullable', 'string', 'max:30'],
            'category' => ['nullable', 'string', 'max:30'],
            'kind' => ['nullable', 'in:tour,excursion'],
            'theme' => ['nullable', 'string', 'max:30'],
            'date' => ['nullable', 'date'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:20'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:popular,price_asc,price_desc,duration'],
        ]);

        $q = Tour::published()->withRating()->with('guide');

        if (! empty($f['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $f['q']) . '%';
            $q->where(fn ($w) => $w->where('title', 'like', $term)->orWhere('location', 'like', $term)
                ->orWhere('tagline', 'like', $term)->orWhere('description', 'like', $term));
        }
        foreach (['country', 'category', 'kind', 'theme'] as $field) {
            if (! empty($f[$field])) {
                $q->where($field, $f[$field]);
            }
        }
        if (isset($f['max_price']) && $f['max_price'] !== null) {
            $q->where('price', '<=', $f['max_price']);
        }
        if (! empty($f['date']) || ! empty($f['guests'])) {
            $from = ! empty($f['date']) ? \Carbon\Carbon::parse($f['date'])->startOfDay() : now();
            $guests = (int) ($f['guests'] ?? 1);
            $q->whereHas('schedules', function ($s) use ($from, $guests) {
                $s->where('status', 'open')->where('starts_at', '>=', $from)
                    ->whereRaw('capacity - (select coalesce(sum(participants_count),0) from bookings where bookings.schedule_id = schedules.id and bookings.status in (\'pending\',\'confirmed\')) >= ?', [$guests]);
            });
        }

        match ($f['sort'] ?? 'popular') {
            'price_asc' => $q->orderBy('price'),
            'price_desc' => $q->orderByDesc('price'),
            'duration' => $q->orderBy('duration'),
            default => $q->orderByDesc('rating_avg')->orderByDesc('reviews_count'),
        };

        return view('tours.index', [
            'tours' => $q->paginate(9)->withQueryString(),
            'filters' => $f,
            'maxPrice' => (int) ceil(Tour::published()->max('price') / 100) * 100,
        ]);
    }

    /** F-02, F-04: страница тура. */
    public function show(Tour $tour)
    {
        abort_unless($tour->publication_status === 'published', 404);

        $tour->load(['guide', 'reviews.user']);
        $schedules = $tour->upcomingSchedules()->get()->each(fn ($s) => $s->free = $s->freeSeats());

        $related = Tour::published()->withRating()->with('guide')
            ->where('id', '!=', $tour->id)
            ->where(fn ($w) => $w->where('country', $tour->country)->orWhere('category', $tour->category))
            ->limit(3)->get();

        return view('tours.show', [
            'tour' => $tour,
            'rating' => $tour->reviews->avg('rating'),
            'schedules' => $schedules,
            'related' => $related,
        ]);
    }

    /** Актуальные данные о местах для страницы бронирования. */
    public function availability(Tour $tour)
    {
        return response()->json(
            $tour->upcomingSchedules()->get()->map(fn ($s) => [
                'id' => $s->id, 'free' => $s->freeSeats(), 'capacity' => $s->capacity,
            ])
        );
    }
}
