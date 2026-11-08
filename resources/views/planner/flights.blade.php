@extends('layouts.app')
@section('title','Авиабилеты — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap" style="padding-bottom:clamp(60px,9vw,130px)">
  <header class="page-head"><div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>Авиабилеты</span></div>
    <h1>Перелёт <em>без суеты</em></h1><p class="lead" style="margin:22px 0 18px">Примерные цены и расписание для планирования поездки.</p><span class="demo-tag">Демонстрационные данные · билеты не продаются</span></header>
  <form class="finder" method="get" action="{{ route('flights') }}" style="margin-bottom:40px"><div class="finder__grid" style="grid-template-columns:1fr 1fr 1fr 1fr auto">
    <div class="field"><label for="from">Откуда</label><select id="from" name="from">@foreach(config('via.origins') as $o)<option @selected(($filters['from']??'Москва')===$o)>{{ $o }}</option>@endforeach</select></div>
    <div class="field"><label for="cty">Куда</label><select id="cty" name="country">@foreach(config('via.countries') as $c=>$d)<option value="{{ $c }}" @selected(($filters['country']??'italy')===$c)>{{ $d['name'] }}</option>@endforeach</select></div>
    <div class="field"><label for="dt">Дата</label><input id="dt" type="date" name="date" value="{{ $filters['date'] ?? now()->addMonth()->toDateString() }}" min="{{ now()->toDateString() }}"></div>
    <div class="field"><label for="gs">Пассажиров</label><input id="gs" type="number" min="1" max="9" name="guests" value="{{ $filters['guests'] ?? 1 }}"></div>
    <button class="btn btn--solid finder__go" type="submit">Показать</button></div></form>
  @forelse($results as $f)
    <div class="flight" data-reveal>
      <div><span class="kicker">{{ $f['airline'] }}</span><small>{{ ru_date($f['date'],'j F') }}</small></div>
      <div><span class="t">{{ $f['depart'] }}</span><small>{{ $f['from'] }}</small></div>
      <div><span class="t">{{ $f['arrive'] }}</span><small>{{ $f['to'] }}</small></div>
      <div><b>{{ $f['duration'] }}</b><small>{{ $f['stops'] ? '1 пересадка' : 'Прямой' }}</small></div>
      <div style="text-align:right"><span class="price">{{ eur($f['price']) }}</span><small>≈ за 1 чел.</small></div>
    </div>
  @empty <div class="empty"><h3>Выберите направление</h3><p class="muted">Укажите страну и дату — мы покажем примерные варианты.</p></div> @endforelse
</div>
@endsection
