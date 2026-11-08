@extends('layouts.app')
@section('title','Администрирование — VIA BELLA')
@section('body-class','page-plain')
@section('no-footer','1')
@section('content')
<div class="wrap adm">
  <nav aria-label="Админ-меню">
    <span class="kicker" style="padding:0 14px 12px">Администратор</span>
    <a class="{{ request()->routeIs('admin.dashboard')?'on':'' }}" href="{{ route('admin.dashboard') }}">Обзор</a>
    <a class="{{ request()->routeIs('admin.tours.*')?'on':'' }}" href="{{ route('admin.tours.index') }}">Туры</a>
    <a class="{{ request()->routeIs('admin.guides.*')?'on':'' }}" href="{{ route('admin.guides.index') }}">Гиды</a>
    <a class="{{ request()->routeIs('admin.bookings')?'on':'' }}" href="{{ route('admin.bookings') }}">Бронирования</a>
    <a class="{{ request()->routeIs('admin.reviews')?'on':'' }}" href="{{ route('admin.reviews') }}">Отзывы</a>
    <a href="{{ route('home') }}">← На сайт</a>
  </nav>
  <div>
    @if(session('status'))<div class="alert" style="margin-bottom:20px">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert--err" style="margin-bottom:20px">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
    @yield('admin')
  </div>
</div>
@endsection
