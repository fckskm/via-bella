<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Guide extends Model
{
    protected $guarded = [];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Tour::class);
    }

    public function languageList(): array
    {
        return array_map('trim', explode(',', $this->languages));
    }

    public function ratingAvg(): ?float
    {
        $v = $this->reviews()->avg('rating');
        return $v ? round($v, 1) : null;
    }

    public function photoUrl(): string
    {
        return asset($this->photo ?: 'img/guides/placeholder.jpg');
    }
}
