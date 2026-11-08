<?php

namespace App\Support;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Support\Collection;

/** Простой подбор по интересам профиля (без внешнего ИИ). */
class Recommender
{
    public static function forInterests(array $interests, int $limit = 3): Collection
    {
        $map = config('via.interests');
        $categories = collect($interests)->map(fn ($i) => $map[$i]['category'] ?? null)->filter()->unique()->values();

        $q = Tour::published()->withRating()->with('guide');
        if ($categories->isNotEmpty()) {
            $q->whereIn('category', $categories);
        }

        $list = $q->orderByDesc('rating_avg')->limit($limit)->get();
        if ($list->count() < $limit) {
            $list = $list->merge(
                Tour::published()->withRating()->with('guide')->whereNotIn('id', $list->pluck('id'))
                    ->orderByDesc('rating_avg')->limit($limit - $list->count())->get()
            );
        }

        return $list;
    }

    public static function forUser(User $user, int $limit = 3): Collection
    {
        return self::forInterests($user->interests ?? [], $limit);
    }
}
