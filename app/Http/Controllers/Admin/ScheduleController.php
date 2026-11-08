<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Tour;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function store(Request $request, Tour $tour)
    {
        $d = $request->validate([
            'starts_at' => ['required', 'date', 'after:now'],
            'capacity' => ['required', 'integer', 'min:1', 'max:200'],
        ]);
        $start = \Carbon\Carbon::parse($d['starts_at']);
        $tour->schedules()->create([
            'starts_at' => $start, 'ends_at' => $start->copy()->addDays(max(0, $tour->duration - 1))->setTime(18, 0),
            'capacity' => $d['capacity'], 'status' => 'open',
        ]);

        return back()->with('status', 'Дата добавлена.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $d = $request->validate(['capacity' => ['required', 'integer', 'min:1', 'max:200'], 'status' => ['required', 'in:open,closed,cancelled']]);
        if ($d['capacity'] < $schedule->bookedSeats()) {
            return back()->withErrors(['capacity' => 'Вместимость не может быть меньше числа уже занятых мест (' . $schedule->bookedSeats() . ').']);
        }
        $schedule->update($d);

        return back()->with('status', 'Сеанс обновлён.');
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->bookings()->exists()) {
            $schedule->update(['status' => 'cancelled']);

            return back()->with('status', 'На дату есть бронирования — она закрыта (отменена), а не удалена.');
        }
        $schedule->delete();

        return back()->with('status', 'Дата удалена.');
    }
}
