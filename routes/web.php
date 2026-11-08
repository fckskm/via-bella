<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlannerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

// Публичная часть
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/vdohnovenie', [HomeController::class, 'journal'])->name('journal');
Route::get('/o-nas', [HomeController::class, 'about'])->name('about');
Route::get('/credits', [HomeController::class, 'credits'])->name('credits');

Route::get('/napravleniya', [DestinationController::class, 'index'])->name('destinations');
Route::get('/napravleniya/{country}', [DestinationController::class, 'show'])->name('destinations.show');
Route::get('/karta', [DestinationController::class, 'map'])->name('map');

Route::get('/tury', [TourController::class, 'index'])->name('tours.index');
Route::get('/tury/{tour}', [TourController::class, 'show'])->name('tours.show');
Route::get('/tury/{tour}/mesta', [TourController::class, 'availability'])->name('tours.availability');

Route::get('/gidy', [GuideController::class, 'index'])->name('guides.index');
Route::get('/gidy/{guide}', [GuideController::class, 'show'])->name('guides.show');

// Демонстрационный планировщик
Route::get('/aviabilety', [PlannerController::class, 'flights'])->name('flights');
Route::get('/oteli', [PlannerController::class, 'hotels'])->name('hotels');
Route::get('/konstruktor', [PlannerController::class, 'builder'])->name('builder');
Route::get('/konstruktor/dannye', [PlannerController::class, 'builderOptions'])->name('builder.options');
Route::post('/konsultant', [AssistantController::class, 'reply'])->name('assistant');

Route::get('/izbrannoe', [FavoriteController::class, 'index'])->name('favorites');
Route::get('/izbrannoe/kartochki', [FavoriteController::class, 'cards'])->name('favorites.cards');

// Гости
Route::middleware('guest')->group(function () {
    Route::get('/vhod', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/vhod', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/registratsiya', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registratsiya', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

// Авторизованные пользователи
Route::middleware('auth')->group(function () {
    Route::post('/vyhod', [AuthController::class, 'logout'])->name('logout');

    Route::get('/kabinet', [AccountController::class, 'index'])->name('account');
    Route::put('/kabinet/profil', [AccountController::class, 'update'])->name('account.update');
    Route::put('/kabinet/parol', [AccountController::class, 'password'])->name('account.password');
    Route::post('/kabinet/bronirovaniya/{booking}/otmena', [AccountController::class, 'cancel'])->name('booking.cancel');

    Route::get('/tury/{tour}/bronirovanie', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/tury/{tour}/bronirovanie', [BookingController::class, 'store'])->name('booking.store')->middleware('throttle:20,1');
    Route::get('/bronirovanie/{booking}', [BookingController::class, 'done'])->name('booking.done');

    Route::post('/bronirovaniya/{booking}/otzyv', [ReviewController::class, 'store'])->name('reviews.store');

    Route::post('/izbrannoe', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/izbrannoe/sinhronizatsiya', [FavoriteController::class, 'merge'])->name('favorites.merge');
});

// Администратор
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('tours', Admin\TourController::class)->except('show');
    Route::resource('guides', Admin\GuideController::class)->except('show');
    Route::post('tours/{tour}/schedules', [Admin\ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('schedules/{schedule}', [Admin\ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('schedules/{schedule}', [Admin\ScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::get('bookings', [Admin\BookingController::class, 'index'])->name('bookings');
    Route::put('bookings/{booking}', [Admin\BookingController::class, 'update'])->name('bookings.update');
    Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews');
    Route::delete('reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
});
