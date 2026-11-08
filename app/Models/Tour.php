<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Tour extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'program' => 'array',
            'included' => 'array',
            'price' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Schedule::class);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('publication_status', 'published');
    }

    public function scopeWithRating(Builder $q): Builder
    {
        return $q->withAvg('reviews as rating_avg', 'rating')->withCount('reviews');
    }

    public function coverUrl(): string
    {
        return img($this->cover);
    }

    public function countryName(): string
    {
        return config("via.countries.{$this->country}.name", $this->country);
    }

    public function ratingValue(): ?float
    {
        $v = $this->rating_avg ?? $this->reviews()->avg('rating');
        return $v ? round($v, 1) : null;
    }

    public function durationText(): string
    {
        return $this->duration_label ?: ($this->duration . ' ' . trans_choice('день|дня|дней', $this->duration));
    }

    public function upcomingSchedules()
    {
        return $this->schedules()->where('status', 'open')->where('starts_at', '>', now())->orderBy('starts_at');
    }
}
