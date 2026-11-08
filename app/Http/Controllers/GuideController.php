<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $q = Guide::withCount('tours');
        if ($c = $request->query('country')) {
            $q->where('country', $c);
        }

        return view('guides.index', ['guides' => $q->orderBy('id')->get(), 'country' => $c]);
    }

    public function show(Guide $guide)
    {
        $tours = $guide->tours()->published()->withRating()->get();
        $reviews = $guide->reviews()->with(['user', 'tour'])->latest('reviews.created_at')->limit(6)->get();

        return view('guides.show', [
            'guide' => $guide,
            'tours' => $tours,
            'reviews' => $reviews,
            'rating' => $guide->ratingAvg(),
            'reviewCount' => $guide->reviews()->count(),
        ]);
    }
}
