<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Tour;
use App\Support\Recommender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $bookings = $user->bookings()->with('schedule.tour.guide', 'review')->latest()->get();

        $upcoming = $bookings->filter(fn ($b) => $b->status !== 'cancelled' && $b->schedule->ends_at->isFuture());
        $history = $bookings->filter(fn ($b) => $b->status !== 'cancelled' && $b->schedule->ends_at->isPast());
        $cancelled = $bookings->where('status', 'cancelled');

        return view('account.index', [
            'user' => $user,
            'upcoming' => $upcoming,
            'history' => $history,
            'cancelled' => $cancelled,
            'recommended' => Recommender::forUser($user, 3),
            'interests' => config('via.interests'),
            'tab' => $request->query('tab', 'bookings'),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string', 'in:' . implode(',', array_keys(config('via.interests')))],
        ]);
        $data['interests'] = $data['interests'] ?? [];
        $user->update($data);

        return redirect()->route('account', ['tab' => 'profile'])->with('status', 'Профиль обновлён.');
    }

    public function password(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);
        $request->user()->update(['password' => $data['password']]);

        return redirect()->route('account', ['tab' => 'profile'])->with('status', 'Пароль изменён.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->isCancellable(), 422, 'Это бронирование уже нельзя отменить.');
        $booking->update(['status' => 'cancelled']);

        return redirect()->route('account')->with('status', 'Бронирование отменено, места возвращены в продажу.');
    }
}
