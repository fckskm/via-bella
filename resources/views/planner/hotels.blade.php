@extends('layouts.app')
@section('title','Отели — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap" style="padding-bottom:clamp(60px,9vw,130px)">
  <header class="page-head"><div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>Отели</span></div>
    <h1>Где <em>остановиться</em></h1><p class="lead" style="margin:22px 0 18px">Подборка бутик-отелей рядом с нашими маршрутами.</p><span class="demo-tag">Демонстрационные данные · бронирование отелей не подключено</span>
    <div class="pill-grid" style="margin-top:26px"><a class="chip {{ !$country?'on':'' }}" href="{{ route('hotels') }}">Все</a>@foreach(config('via.countries') as $c=>$d)<a class="chip {{ $country===$c?'on':'' }}" href="{{ route('hotels',['country'=>$c]) }}">{{ $d['name'] }}</a>@endforeach</div></header>
  @foreach($hotels as $h)@include('planner._hotel',['h'=>$h])@endforeach
  <div style="margin-top:44px"><a class="btn btn--solid" href="{{ route('builder') }}">Собрать путешествие целиком <svg><use href="#i-arrow"/></svg></a></div>
</div>
@endsection
