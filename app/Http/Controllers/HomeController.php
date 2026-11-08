<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Review;
use App\Models\Tour;
use App\Support\Recommender;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $featured = Tour::published()->withRating()->with('guide')
            ->where('kind', 'tour')->orderByDesc('rating_avg')->limit(5)->get();

        $recommended = $request->user()
            ? Recommender::forUser($request->user(), 3)
            : collect();

        return view('home', [
            'featured' => $featured,
            'excursions' => Tour::published()->withRating()->with('guide')->where('kind', 'excursion')->limit(3)->get(),
            'guides' => Guide::withCount('tours')->orderBy('id')->limit(4)->get(),
            'reviews' => Review::with(['user', 'tour'])->where('rating', '>=', 5)->latest()->limit(3)->get(),
            'recommended' => $recommended,
            'tourCount' => Tour::published()->count(),
            'guideCount' => Guide::count(),
        ]);
    }

    public function journal()
    {
        return view('pages.journal');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function credits()
    {
        return view('pages.credits');
    }
}
