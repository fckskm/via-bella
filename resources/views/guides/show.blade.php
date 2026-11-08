@extends('layouts.app')
@section('title',$guide->name.' — гид VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap">
  <div class="crumbs" style="padding-top:36px"><a href="{{ route('home') }}">Главная</a><i>/</i><a href="{{ route('guides.index') }}">Гиды</a><i>/</i><span>{{ $guide->name }}</span></div>
  <section class="g-hero">
    <div class="g-hero__ph ph" data-reveal><img src="{{ $guide->photoUrl() }}" alt="Портрет: {{ $guide->name }}"></div>
    <div data-reveal style="--d:.12s">
      <span class="kicker">Гид · {{ config("via.countries.{$guide->country}.name") }}</span>
      @php $parts = explode(' ', $guide->name, 2); @endphp
      <h1 style="margin-top:16px">{{ $parts[0] }}<em>{{ $parts[1] ?? '' }}</em></h1>
      <p class="lead" style="margin-top:22px">{{ $guide->description }}</p>
      <div class="g-meta">
        <div><small>Город</small><b>{{ $guide->city }}</b></div>
        <div><small>Опыт</small><b>{{ $guide->experience_years }} лет</b></div>
        <div><small>Рейтинг</small><b>{{ $rating ?: '—' }} @if($reviewCount)<span class="muted" style="font-size:.9rem">· {{ $reviewCount }}</span>@endif</b></div>
        <div style="grid-column:1 / -1"><small>Специализация</small><b>{{ $guide->specialization }}</b></div>
        <div style="grid-column:1 / -1"><small>Языки</small><b>{{ $guide->languages }}</b></div>
      </div>
      <div style="display:flex;gap:14px;flex-wrap:wrap"><a class="btn btn--solid" href="#routes">Авторские маршруты <svg><use href="#i-arrow"/></svg></a><button class="btn" data-fav="guide:{{ $guide->slug }}" aria-pressed="false">В избранное</button></div>
    </div>
  </section>
</div>

<section class="sec sec--linen">
  <div class="wrap">
    <p class="pull" data-reveal>{{ $guide->quote }}</p>
    <div class="g-story" style="margin-top:clamp(36px,5vw,70px)" data-reveal>
      @foreach(preg_split('/\n+/', $guide->story ?? $guide->description) as $p)<p>{{ $p }}</p>@endforeach
      @if($guide->contact_information)<p class="muted" style="font-size:14px">Связаться: {{ $guide->contact_information }}</p>@endif
    </div>
  </div>
</section>

<section class="sec" id="routes">
  <div class="wrap">
    <div class="sec-head sec-head--row"><h2 class="h2">Авторские <em>маршруты</em></h2></div>
    <div class="grid-cards">@foreach($tours as $t)@include('partials.tour-card',['tour'=>$t,'showSub'=>true,'delay'=>$loop->index*.08])@endforeach</div>
  </div>
</section>

@if($reviews->isNotEmpty())
<section class="sec sec--pist">
  <div class="wrap">
    <h2 class="h2" style="margin-bottom:34px">Что говорят <em>путешественники</em></h2>
    <div class="row3">
      @foreach($reviews->take(3) as $r)
      <article class="story" data-reveal style="--d:{{ $loop->index*.08 }}s"><div class="story__b">
        <blockquote>{{ \Illuminate\Support\Str::limit($r->text,170) }}</blockquote>
        <div class="who">@if($r->user->avatar)<img src="{{ asset($r->user->avatar) }}" alt="">@else<span class="ava">{{ $r->user->initials() }}</span>@endif<div><b>{{ $r->user->name }}</b><span>{{ $r->tour->title }}</span></div><span style="margin-left:auto">@include('partials.stars',['value'=>$r->rating])</span></div>
      </div></article>
      @endforeach
    </div>
  </div>
</section>
@endif
@endsection
