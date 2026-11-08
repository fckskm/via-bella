@extends('layouts.app')
@section('title',$country['name'].' — VIA BELLA')
@section('content')
<section class="t-hero" data-hero style="min-height:min(86svh,760px)">
  <div class="bg"><img src="{{ img($country['img']) }}" alt="{{ $country['name'] }}"></div>
  <div class="t-hero__in">
    <div><div class="crumbs" style="color:rgba(255,255,255,.8)"><a href="{{ route('destinations') }}">Направления</a><i>/</i><span>{{ $country['name'] }}</span></div>
      <span class="kicker light">{{ $country['name_en'] }}</span><h1 style="margin-top:14px" data-reveal>{{ $country['name'] }}</h1><p>{{ $country['lead'] }}</p></div>
    <div class="facts"><div><small>Маршрутов</small><b>{{ $tours->count() }}</b></div><div><small>Места</small><b>{{ count($country['cities']) }}</b></div></div>
  </div>
</section>
<div class="wrap">
  <section class="sec" style="padding-bottom:0"><div class="pill-grid" data-reveal>@foreach($country['cities'] as $city=>$_)<span class="chip chip--soft">{{ $city }}</span>@endforeach</div></section>
  <section class="sec">
    <div class="sec-head sec-head--row"><h2 class="h2">Маршруты: <em>{{ $country['name'] }}</em></h2><a class="link" href="{{ route('tours.index',['country'=>$code]) }}">Все в каталоге <svg><use href="#i-arrow"/></svg></a></div>
    <div class="grid-cards">@foreach($tours as $t)@include('partials.tour-card',['tour'=>$t,'showSub'=>true,'delay'=>($loop->index%3)*.08])@endforeach</div>
  </section>
  @if($hotels->count())
  <section class="sec" style="padding-top:0">
    <div class="sec-head sec-head--row"><h2 class="h2">Где <em>остановиться</em></h2><span class="demo-tag">Демо-данные</span></div>
    @foreach($hotels as $h)@include('planner._hotel',['h'=>$h])@endforeach
  </section>
  @endif
</div>
@endsection
