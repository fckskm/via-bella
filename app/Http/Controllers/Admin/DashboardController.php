<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guide;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'stats' => [
                'Туров' => Tour::count(), 'Гидов' => Guide::count(), 'Пользователей' => User::count(),
                'Бронирований' => Booking::count(), 'Отзывов' => Review::count(),
            ],
            'latest' => Booking::with('user', 'schedule.tour')->latest()->limit(8)->get(),
        ]);
    }
}
