@extends('layouts.app')
@section('title','Направления — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap">
  <header class="page-head"><div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>Направления</span></div>
    <h1>Пять стран, <em>один журнал</em></h1><p class="lead" style="margin-top:22px">Италия, Франция, Греция, Швейцария и Австрия — выберите страну или найдите точку на карте.</p></header>
  <div class="dest" style="padding-bottom:clamp(60px,9vw,130px)">
    @php $i=0; @endphp
    @foreach(config('via.countries') as $code=>$c) @php $i++; @endphp
      <a class="d{{ $i }}" href="{{ route('destinations.show',$code) }}" data-reveal style="--d:{{ $i*.06 }}s"><div class="ph"><img src="{{ img($c['img']) }}" alt="{{ $c['name'] }}" loading="lazy"></div>
        <div class="dest__t"><span>{{ $c['lead'] }}</span><b>{{ $c['name'] }}</b></div><span class="dest__go"><svg><use href="#i-arrow"/></svg></span></a>
    @endforeach
    <div class="d6" data-reveal><p>Не знаете, с чего начать?<small><a href="{{ route('map') }}">Откройте карту →</a></small></p></div>
  </div>
</div>
@endsection
