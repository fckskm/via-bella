@extends('layouts.app')
@section('title','Конструктор путешествия — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap" style="padding-bottom:clamp(60px,9vw,130px)">
  <header class="page-head"><div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>Конструктор</span></div>
    <h1>Соберите <em>своё путешествие</em></h1><p class="lead" style="margin:22px 0 18px">Направление, даты, перелёт, проживание и впечатления — маршрут и итоговая стоимость появятся справа.</p><span class="demo-tag">Перелёты и отели — демонстрационные данные · экскурсии — реальные из каталога</span></header>
  <div class="builder" id="builder" data-url="{{ route('builder.options') }}">
    <div>
      <div class="bstep" style="border-top:0;padding-top:0">
        <h3><span>01</span>Направление и даты</h3>
        <div class="finder__grid" style="grid-template-columns:repeat(2,1fr);border:1px solid var(--line)">
          <div class="field"><label for="bCountry">Страна</label><select id="bCountry"><option value="">Выберите…</option>@foreach($countries as $c=>$d)<option value="{{ $c }}">{{ $d['name'] }}</option>@endforeach</select></div>
          <div class="field"><label for="bFrom">Вылет из</label><select id="bFrom">@foreach(config('via.origins') as $o)<option>{{ $o }}</option>@endforeach</select></div>
          <div class="field"><label for="bDate">Дата вылета</label><input id="bDate" type="date" value="{{ now()->addMonth()->toDateString() }}" min="{{ now()->toDateString() }}"></div>
          <div class="field"><label>Путешественники</label><div class="stepper" data-stepper data-min="1" data-max="9"><button type="button" data-d="-">−</button><output>2</output><input type="hidden" id="bGuests" value="2"><button type="button" data-d="+">+</button></div></div>
          <div class="field" style="grid-column:1 / -1;border-bottom:0"><label for="bNights">Ночей: <span id="bNightsOut">5</span></label><input id="bNights" type="range" min="2" max="14" value="5" style="accent-color:var(--olive);width:100%"></div>
        </div>
        <button type="button" class="btn btn--solid" id="bGo" style="margin-top:22px">Подобрать варианты <svg><use href="#i-arrow"/></svg></button>
      </div>
      <div id="bBody" hidden>
        <div class="bstep"><h3><span>02</span>Перелёт</h3><div class="choice-grid" id="bFlights"></div></div>
        <div class="bstep"><h3><span>03</span>Проживание</h3><div class="choice-grid" id="bHotels"></div></div>
        <div class="bstep"><h3><span>04</span>Впечатления</h3><div class="choice-grid" id="bTours"></div></div>
      </div>
    </div>
    <aside class="route"><span class="kicker">Ваш маршрут</span><ol id="bRoute"><li class="empty-li">Выберите направление, чтобы начать</li></ol>
      <div class="total"><span class="lbl">Ориентировочно</span><b id="bTotal">€0</b></div>
      <div id="bLinks" style="display:grid;gap:8px"></div>
      <p class="muted" style="font-size:12px;margin:0">Расчёт демонстрационный. Реальное бронирование доступно для туров и экскурсий каталога.</p></aside>
  </div>
</div>
@endsection
