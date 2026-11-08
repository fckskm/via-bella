<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $guarded = [];

    public const STATUSES = [
        'pending' => 'Заявка принята',
        'confirmed' => 'Подтверждено',
        'cancelled' => 'Отменено',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /** Поездка состоялась: бронирование не отменено, а сеанс уже завершён. */
    public function isCompleted(): bool
    {
        return $this->status !== 'cancelled' && $this->schedule->ends_at->isPast();
    }

    public function isCancellable(): bool
    {
        return $this->status !== 'cancelled' && $this->schedule->starts_at->isFuture();
    }
}
