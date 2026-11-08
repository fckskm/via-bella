@extends('layouts.app')
@section('title','Гиды — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap">
  <header class="page-head">
    <div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>Гиды</span></div>
    <h1>Люди, которые <em>знают дорогу</em></h1>
    <p class="lead" style="margin-top:22px">Наши гиды — историки, сомелье, капитаны и горные проводники. Каждый ведёт только те маршруты, которые придумал сам.</p>
    <div class="pill-grid" style="margin-top:28px"><a class="chip {{ !$country?'on':'' }}" href="{{ route('guides.index') }}">Все</a>@foreach(config('via.countries') as $c=>$d)<a class="chip {{ $country===$c?'on':'' }}" href="{{ route('guides.index',['country'=>$c]) }}">{{ $d['name'] }}</a>@endforeach</div>
  </header>
  <div class="g-list" style="padding-bottom:clamp(60px,9vw,130px)">
    @foreach($guides as $g)
    <article class="gcard" data-reveal style="--d:{{ ($loop->index%3)*.08 }}s">
      <a class="ph" href="{{ route('guides.show',$g) }}"><img src="{{ $g->photoUrl() }}" alt="{{ $g->name }}, гид" loading="lazy"></a>
      <button class="fav" data-fav="guide:{{ $g->slug }}" aria-label="В избранное" aria-pressed="false"><svg><use href="#i-heart"/></svg></button>
      <div class="b">
        <span class="kicker">{{ config("via.countries.{$g->country}.name") }} · {{ $g->city }}</span>
        <h3 style="margin-top:8px"><a href="{{ route('guides.show',$g) }}">{{ $g->name }}</a></h3>
        <p class="muted" style="margin:8px 0 12px;font-size:14.5px">{{ $g->specialization }}</p>
        <div style="display:flex;gap:16px;align-items:center;justify-content:space-between"><span class="muted" style="font-size:12px">{{ $g->tours_count }} {{ trans_choice('маршрут|маршрута|маршрутов',$g->tours_count) }} · {{ $g->experience_years }} лет</span><a class="link" href="{{ route('guides.show',$g) }}">Профиль <svg><use href="#i-arrow"/></svg></a></div>
      </div>
    </article>
    @endforeach
  </div>
</div>
@endsection
