<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /** F-08: отзыв можно оставить только о состоявшейся поездке и один раз. */
    public function store(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        $booking->load('schedule');

        if (! $booking->isCompleted()) {
            return back()->withErrors(['text' => 'Отзыв можно оставить после завершения поездки.']);
        }
        if ($booking->review()->exists()) {
            return back()->withErrors(['text' => 'Вы уже оставили отзыв об этой поездке.']);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:120'],
            'text' => ['required', 'string', 'min:20', 'max:2000'],
        ], [
            'text.min' => 'Расскажите чуть подробнее — хотя бы пару предложений (от 20 символов).',
            'rating.required' => 'Поставьте оценку.',
        ]);

        Review::create($data + [
            'user_id' => $request->user()->id,
            'tour_id' => $booking->schedule->tour_id,
            'booking_id' => $booking->id,
        ]);

        return redirect()->route('account', ['tab' => 'history'])->with('status', 'Спасибо! Ваш отзыв опубликован.');
    }
}
