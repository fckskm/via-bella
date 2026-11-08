@extends('layouts.app')
@section('title','Вдохновение — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap" style="padding-bottom:clamp(60px,9vw,130px)">
  <header class="page-head"><div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>Вдохновение</span></div>
    <h1>Страницы <em>журнала</em></h1><p class="lead" style="margin-top:22px">Эссе, идеи и атмосферные заметки. Читайте — и пусть захочется уехать.</p></header>
  <div class="insp" style="margin-bottom:clamp(60px,8vw,120px)">
    @foreach(config('via.journal') as $j)
      <article class="ins {{ $j['size'] }}" id="{{ $j['slug'] }}" data-reveal><div class="ph"><img src="{{ img($j['img']) }}" alt="{{ $j['title'] }}" loading="lazy"></div><span class="kicker">{{ $j['kicker'] }}</span><h3>{{ $j['title'] }}</h3><p class="muted" style="font-size:14.5px;margin-top:8px">{{ $j['text'] }}</p></article>
    @endforeach
  </div>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px" class="row3">
    @foreach(config('via.quotes') as $q)<p class="pull" style="font-size:clamp(1.6rem,2.4vw,2.4rem);padding:30px;background:{{ ['var(--pistachio)','var(--linen)','#F6DDD5'][$loop->index] }}" data-reveal>{{ $q }}</p>@endforeach
  </div>
</div>
@endsection
