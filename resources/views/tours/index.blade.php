@extends('layouts.app')
@php
  $kind = $filters['kind'] ?? null;
  $title = $kind === 'excursion' ? 'Экскурсии' : ($kind === 'tour' ? 'Туры' : 'Каталог');
  $qs = fn(array $o) => route('tours.index', array_filter(array_merge($filters, $o, ['page'=>null]), fn($v)=>$v!==null&&$v!==''));
@endphp
@section('title', $title.' — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap">
  <header class="page-head">
    <div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>{{ $title }}</span></div>
    <h1>@if($kind==='excursion')Экскурсии <em>и впечатления</em>@elseif($kind==='tour')Туры <em>по Европе</em>@else Каталог <em>путешествий</em>@endif</h1>
    <p class="lead" style="margin-top:22px">Маршруты на несколько дней и авторские экскурсии на один день. Фильтры помогут найти поездку по стране, настроению и бюджету.</p>
  </header>

  <div class="catalog">
    <form class="filters" method="get" action="{{ route('tours.index') }}" id="filterForm">
      <div><h4>Поиск</h4><input class="in" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Город, название…"></div>
      <div><h4>Формат</h4><div class="opt">
        <a class="{{ !$kind?'on':'' }}" href="{{ $qs(['kind'=>null]) }}">Все</a>
        <a class="{{ $kind==='tour'?'on':'' }}" href="{{ $qs(['kind'=>'tour']) }}">Многодневные туры</a>
        <a class="{{ $kind==='excursion'?'on':'' }}" href="{{ $qs(['kind'=>'excursion']) }}">Экскурсии на день</a></div></div>
      <div><h4>Страна</h4><div class="opt">
        <a class="{{ empty($filters['country'])?'on':'' }}" href="{{ $qs(['country'=>null]) }}">Все страны</a>
        @foreach(config('via.countries') as $c=>$d)<a class="{{ ($filters['country']??'')===$c?'on':'' }}" href="{{ $qs(['country'=>$c]) }}">{{ $d['name'] }}</a>@endforeach</div></div>
      <div><h4>Настроение</h4><div class="opt">
        <a class="{{ empty($filters['category'])?'on':'' }}" href="{{ $qs(['category'=>null]) }}">Любое</a>
        @foreach(config('via.categories') as $k=>$d)<a class="{{ ($filters['category']??'')===$k?'on':'' }}" href="{{ $qs(['category'=>$k]) }}">{{ $d['name'] }}</a>@endforeach</div></div>
      @if($kind==='excursion')<div><h4>Тема</h4><div class="opt">
        <a class="{{ empty($filters['theme'])?'on':'' }}" href="{{ $qs(['theme'=>null]) }}">Любая</a>
        @foreach(config('via.themes') as $k=>$t)<a class="{{ ($filters['theme']??'')===$k?'on':'' }}" href="{{ $qs(['theme'=>$k]) }}">{{ $t }}</a>@endforeach</div></div>@endif
      <div class="fg"><h4 style="margin:0">Бюджет до <span id="mpOut" class="num">{{ eur($filters['max_price'] ?? $maxPrice) }}</span></h4>
        <input type="range" name="max_price" min="100" max="{{ $maxPrice }}" step="50" value="{{ $filters['max_price'] ?? $maxPrice }}" style="accent-color:var(--olive);width:100%" oninput="document.getElementById('mpOut').textContent='€'+Number(this.value).toLocaleString('ru-RU')"></div>
      <div class="row2" style="gap:12px">
        <div class="fg"><h4 style="margin:0">Дата от</h4><input class="in" type="date" name="date" value="{{ $filters['date'] ?? '' }}"></div>
        <div class="fg"><h4 style="margin:0">Гостей</h4><input class="in" type="number" min="1" max="20" name="guests" value="{{ $filters['guests'] ?? '' }}" placeholder="—"></div>
      </div>
      @foreach(['kind','country','category','theme'] as $h)@if(!empty($filters[$h]))<input type="hidden" name="{{ $h }}" value="{{ $filters[$h] }}">@endif @endforeach
      <div style="display:flex;gap:12px;flex-wrap:wrap"><button class="btn btn--solid btn--sm" type="submit">Применить</button><a class="btn btn--sm" href="{{ route('tours.index') }}">Сбросить</a></div>
    </form>

    <div>
      <div class="toolbar">
        <span class="muted">Найдено: <b class="num">{{ $tours->total() }}</b></span>
        <form method="get" style="display:flex;gap:10px;align-items:center">
          @foreach($filters as $k=>$v)@if($k!=='sort' && $v!==null && $v!=='')<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif @endforeach
          <label class="lbl" for="sort">Сортировка</label>
          <select class="in" style="width:auto;padding-right:24px" id="sort" name="sort" onchange="this.form.submit()">
            @foreach(['popular'=>'По рейтингу','price_asc'=>'Сначала дешевле','price_desc'=>'Сначала дороже','duration'=>'По длительности'] as $v=>$l)<option value="{{ $v }}" @selected(($filters['sort']??'popular')===$v)>{{ $l }}</option>@endforeach
          </select>
        </form>
      </div>
      @if($tours->isEmpty())
        <div class="empty"><h3>Ничего не нашли</h3><p class="muted">Попробуйте изменить фильтры или сбросить их — Европа большая.</p><a class="btn" href="{{ route('tours.index') }}">Сбросить фильтры</a></div>
      @else
        <div class="grid-cards">@foreach($tours as $t)@include('partials.tour-card',['tour'=>$t,'showSub'=>true,'delay'=>($loop->index%3)*.08])@endforeach</div>
        @if($tours->hasPages())
        <nav class="pager" aria-label="Страницы">
          @foreach($tours->getUrlRange(1,$tours->lastPage()) as $p=>$u)
            @if($p==$tours->currentPage())<span class="cur">{{ $p }}</span>@else<a href="{{ $u }}">{{ $p }}</a>@endif
          @endforeach
        </nav>
        @endif
      @endif
    </div>
  </div>
</div>
<div style="height:clamp(60px,9vw,130px)"></div>
@endsection
