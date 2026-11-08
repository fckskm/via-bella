@extends('layouts.app')
@section('title', $tour->title.' — VIA BELLA')
@section('description', $tour->tagline)
@section('content')
@php $gal = collect($tour->gallery ?: [$tour->cover]); @endphp
<section class="t-hero" data-hero>
  <div class="bg"><img src="{{ img($tour->cover) }}" alt="{{ $tour->title }}" fetchpriority="high"></div>
  <div class="t-hero__in">
    <div>
      <div class="crumbs" style="color:rgba(255,255,255,.8)"><a href="{{ route('tours.index') }}">Каталог</a><i>/</i><a href="{{ route('destinations.show',$tour->country) }}">{{ $tour->countryName() }}</a><i>/</i><span>{{ $tour->location }}</span></div>
      <h1 data-reveal>{{ $tour->title }}</h1>
      <p data-reveal style="--d:.15s">{{ $tour->tagline }}</p>
    </div>
    <div class="facts" data-reveal style="--d:.25s">
      <div><small>Длительность</small><b>{{ $tour->durationText() }}</b></div>
      <div><small>Цена от</small><b>{{ eur($tour->price) }}</b></div>
      <div><small>Рейтинг</small><b>{{ $rating ? number_format($rating,1,',','') : '—' }} <span style="font-size:.9rem">/ 5</span></b></div>
    </div>
  </div>
</section>

<div class="wrap" style="padding-top:clamp(40px,6vw,90px);padding-bottom:clamp(60px,8vw,120px)">
  <div class="t-layout">
    <div class="t-main">
      <section data-reveal>
        <div class="kicker" style="margin-bottom:18px">{{ $tour->location }} · {{ $tour->countryName() }}</div>
        <p class="t-lead">{{ $tour->description }}</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px">
          <button class="btn btn--sm" data-fav="tour:{{ $tour->slug }}" aria-pressed="false"><svg style="width:16px"><use href="#i-heart"/></svg> В избранное</button>
          <a class="btn btn--sm" href="{{ route('destinations.show',$tour->country) }}">Все направления: {{ $tour->countryName() }}</a>
        </div>
      </section>

      @if($gal->count()>1)
      <section data-reveal><div class="mosaic n{{ min(6,$gal->count()) }}">
        @foreach($gal->take(6) as $g)<div class="ph"><img src="{{ img($g) }}" alt="{{ $tour->title }} — фото {{ $loop->iteration }}" loading="lazy"></div>@endforeach
      </div></section>
      @endif

      <section data-reveal>
        <div class="sec-head__top" style="margin-bottom:26px"><span class="kicker">Программа</span><i></i></div>
        <h2 class="h2" style="margin-bottom:34px">День за днём</h2>
        <div class="program">
          @foreach($tour->program ?? [] as $i=>$d)
          <div class="day"><div class="day__n">@if($d['day'])<b>{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</b>{{ $d['day'] }}@else<b>—</b>@endif</div><div><h3>{{ $d['title'] }}</h3><p>{{ $d['text'] }}</p></div></div>
          @endforeach
        </div>
      </section>

      <section data-reveal>
        <h2 class="h3" style="margin-bottom:22px">Что входит</h2>
        <ul class="inc">@foreach($tour->included ?? [] as $x)<li><svg><use href="#i-check"/></svg>{{ $x }}</li>@endforeach</ul>
      </section>

      <section data-reveal>
        <div class="guide-note">
          <a class="ph" href="{{ route('guides.show',$tour->guide) }}"><img src="{{ $tour->guide->photoUrl() }}" alt="{{ $tour->guide->name }}" loading="lazy"></a>
          <div>
            <span class="kicker">Ваш гид</span>
            <q>{{ $tour->guide->quote }}</q>
            <b style="font:400 1.6rem var(--serif);display:block">{{ $tour->guide->name }}</b>
            <p class="muted" style="margin:6px 0 16px">{{ $tour->guide->city }}, {{ config('via.countries.'.$tour->guide->country.'.name') }} · {{ $tour->guide->languages }}</p>
            <a class="link" href="{{ route('guides.show',$tour->guide) }}">История гида <svg><use href="#i-arrow"/></svg></a>
          </div>
        </div>
      </section>

      <section data-reveal id="reviews">
        <div style="display:flex;justify-content:space-between;align-items:end;gap:20px;flex-wrap:wrap;margin-bottom:10px">
          <h2 class="h2">Отзывы</h2>
          <div class="score"><b>{{ $rating ? number_format($rating,1,',','') : '—' }}</b><span class="muted">{{ $tour->reviews->count() }} {{ trans_choice('отзыв|отзыва|отзывов',$tour->reviews->count()) }}</span></div>
        </div>
        @forelse($tour->reviews->sortByDesc('created_at') as $r)
          <article class="rev">
            @if($r->user->avatar)<img class="ava" src="{{ asset($r->user->avatar) }}" alt="{{ $r->user->name }}" loading="lazy">@else<span class="ava">{{ $r->user->initials() }}</span>@endif
            <div>
              @if($r->title)<span class="kicker">{{ $r->title }}</span>@endif
              <blockquote style="margin-top:8px">{{ $r->text }}</blockquote>
              <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap"><b style="font-size:13px">{{ $r->user->name }}</b><span class="muted" style="font-size:12px">{{ $r->user->city }} · {{ ru_date($r->created_at,'F Y') }}</span>@include('partials.stars',['value'=>$r->rating])</div>
              @if($r->photo)<div class="ph rev__ph"><img src="{{ img($r->photo) }}" alt="Фото из поездки" loading="lazy"></div>@endif
            </div>
          </article>
        @empty
          <p class="muted" style="padding:30px 0">Отзывов пока нет. Станьте первым — после поездки оставьте отзыв в личном кабинете.</p>
        @endforelse
      </section>
    </div>

    <aside class="aside" id="tourAside" data-reveal>
      <div><span class="kicker">Стоимость</span><div class="price"><small>от</small>{{ eur($tour->price) }}</div><span class="muted" style="font-size:13px">за человека · {{ $tour->durationText() }}</span></div>
      <div>
        <span class="lbl" style="display:block;margin-bottom:12px">Ближайшие даты</span>
        <div class="dates">
          @forelse($schedules->take(6) as $s)
            @php $free = $s->free; $pct = 100 - ($free/$s->capacity*100); @endphp
            <label class="date-opt {{ $free===0?'full':'' }} {{ $free>0&&$free<=3?'low':'' }}">
              <input class="sr" type="radio" name="sched" value="{{ $s->id }}" @disabled($free===0)>
              <span><b>{{ ru_date($s->starts_at,'j F') }}@if($tour->duration>1) — {{ ru_date($s->ends_at,'j F') }}@endif</b><div class="bar"><i style="width:{{ $pct }}%"></i></div></span>
              <small>{{ $free===0 ? 'Мест нет' : ($free<=3 ? 'Осталось '.$free : 'Свободно '.$free) }}<br>из {{ $s->capacity }}</small>
            </label>
          @empty
            <p class="muted" style="margin:0">Новые даты скоро появятся.</p>
          @endforelse
        </div>
      </div>
      @if($schedules->isNotEmpty())
        <a class="btn btn--solid" id="goBook" data-href="{{ route('booking.create',$tour) }}" href="{{ route('booking.create',$tour) }}" style="justify-content:center">Забронировать <svg><use href="#i-arrow"/></svg></a>
      @endif
      <p class="muted" style="font-size:12.5px;margin:0">Оплата не списывается: после заявки мы свяжемся с вами для подтверждения. Бесплатная отмена до начала поездки.</p>
    </aside>
  </div>
</div>

@if($related->isNotEmpty())
<section class="sec sec--linen"><div class="wrap">
  <div class="sec-head sec-head--row"><h2 class="h2">Вам <em>может понравиться</em></h2><a class="link" href="{{ route('tours.index') }}">Весь каталог <svg><use href="#i-arrow"/></svg></a></div>
  <div class="grid-cards">@foreach($related as $t)@include('partials.tour-card',['tour'=>$t,'delay'=>$loop->index*.08])@endforeach</div>
</div></section>
@endif
@endsection
