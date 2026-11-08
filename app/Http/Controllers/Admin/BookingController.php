<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $q = Booking::with('user', 'schedule.tour')->latest();
        if ($s = $request->query('status')) {
            $q->where('status', $s);
        }

        return view('admin.bookings', ['bookings' => $q->paginate(20)->withQueryString(), 'status' => $s]);
    }

    public function update(Request $request, Booking $booking)
    {
        $d = $request->validate(['status' => ['required', 'in:pending,confirmed,cancelled']]);
        $booking->update($d);

        return back()->with('status', "Статус заявки {$booking->reference} изменён.");
    }
}
