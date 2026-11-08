<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Количество занятых мест: активные заявки (ожидает + подтверждено). */
    public function bookedSeats(): int
    {
        return (int) $this->bookings()->whereIn('status', ['pending', 'confirmed'])->sum('participants_count');
    }

    public function freeSeats(): int
    {
        return max(0, $this->capacity - $this->bookedSeats());
    }

    public function isBookable(): bool
    {
        return $this->status === 'open' && $this->starts_at->isFuture() && $this->freeSeats() > 0;
    }
}
