<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /** Пошаговая форма бронирования (F-05). */
    public function create(Request $request, Tour $tour)
    {
        abort_unless($tour->publication_status === 'published', 404);

        $schedules = $tour->upcomingSchedules()->get()->each(fn ($s) => $s->free = $s->freeSeats());

        return view('booking.create', [
            'tour' => $tour->load('guide'),
            'schedules' => $schedules,
            'preselected' => (int) $request->query('schedule'),
            'guests' => max(1, (int) $request->query('guests', 2)),
        ]);
    }

    /** Сохранение. Все проверки мест выполняются на сервере внутри транзакции (F-06). */
    public function store(Request $request, Tour $tour)
    {
        $data = $request->validate([
            'schedule_id' => ['required', 'integer', 'exists:schedules,id'],
            'participants_count' => ['required', 'integer', 'min:1', 'max:20'],
            'contact_name' => ['required', 'string', 'min:2', 'max:100'],
            'contact_email' => ['required', 'email', 'max:120'],
            'contact_phone' => ['required', 'string', 'min:6', 'max:40'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'agree' => ['accepted'],
        ], [
            'schedule_id.required' => 'Выберите дату поездки.',
            'participants_count.min' => 'Укажите хотя бы одного участника.',
            'agree.accepted' => 'Подтвердите, что ознакомились с условиями.',
        ]);

        $booking = DB::transaction(function () use ($data, $tour, $request) {
            // Блокируем строку сеанса: параллельные заявки выстраиваются в очередь.
            $schedule = Schedule::whereKey($data['schedule_id'])->lockForUpdate()->first();

            if (! $schedule || $schedule->tour_id !== $tour->id) {
                throw ValidationException::withMessages(['schedule_id' => 'Выбранная дата не относится к этому туру.']);
            }
            if ($schedule->status !== 'open' || ! $schedule->starts_at->isFuture()) {
                throw ValidationException::withMessages(['schedule_id' => 'Бронирование на эту дату закрыто.']);
            }
            $free = $schedule->freeSeats();
            if ($data['participants_count'] > $free) {
                throw ValidationException::withMessages([
                    'participants_count' => $free > 0
                        ? "На эту дату осталось только мест: {$free}. Уменьшите число участников или выберите другую дату."
                        : 'На эту дату свободных мест не осталось. Выберите другую дату.',
                ]);
            }

            $unit = (float) $tour->price;

            return Booking::create([
                'reference' => 'VB-' . strtoupper(Str::random(6)),
                'user_id' => $request->user()->id,
                'schedule_id' => $schedule->id,
                'participants_count' => $data['participants_count'],
                'status' => 'pending',
                'unit_price' => $unit,
                'total_price' => $unit * $data['participants_count'],
                'contact_name' => $data['contact_name'],
                'contact_email' => $data['contact_email'],
                'contact_phone' => $data['contact_phone'],
                'comment' => $data['comment'] ?? null,
            ]);
        });

        return redirect()->route('booking.done', $booking);
    }

    public function done(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        return view('booking.done', ['booking' => $booking->load('schedule.tour.guide')]);
    }
}
