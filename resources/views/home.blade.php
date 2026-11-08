@extends('layouts.app')
@section('title', 'VIA BELLA — European Travel Collection')
@section('body-class', 'page-home')
@section('content')

{{-- ============ ОБЛОЖКА ============ --}}
<section class="hero" data-hero>
  <div class="hero__bg"><img src="{{ img('places/hero') }}" alt="Побережье Амальфи на закате" fetchpriority="high"></div>
  <span class="hero__vert">Выпуск 01 · Лето в Европе</span>
  <div class="hero__in">
    <div>
      <div class="hero__eyebrow" data-reveal><span class="kicker light">European Travel Collection</span><i></i><span class="kicker light">Выпуск 01</span></div>
      <h1 data-reveal style="--d:.1s">Мир прекраснее,<em>чем ты представляешь</em></h1>
      <p class="hero__text" data-reveal style="--d:.25s">Путешествия по Европе, которые хочется листать, как страницы глянцевого журнала, — и проживать на самом деле. Авторские маршруты, гиды с историей и места, где время идёт медленнее.</p>
      <div class="hero__cta" data-reveal style="--d:.4s">
        <a class="btn btn--light" href="#destinations">Открыть мир <svg><use href="#i-arrow"/></svg></a>
        <a class="link" href="{{ route('tours.index') }}" style="color:#fff">Каталог туров <svg><use href="#i-arrow"/></svg></a>
      </div>
    </div>
    <div class="hero__side" data-reveal style="--d:.5s">
      <div class="hero__frame ph"><img src="{{ img('places/tuscany') }}" alt="Тоскана" loading="eager"></div>
      <div class="hero__frame hero__frame--b ph"><img src="{{ img('places/santorini') }}" alt="Санторини" loading="eager"></div>
      <p class="hero__coord">40°38′ N · 14°29′ E<br>Позитано, Италия</p>
    </div>
  </div>
</section>

<div class="marquee" aria-hidden="true"><div class="marquee__t">
  @for($i=0;$i<2;$i++)
  <span>Амальфи</span><span>Ривьера</span><span>Тоскана</span><span>Санторини</span><span>Бернские Альпы</span><span>Прованс</span><span>Гальштат</span><span>Париж</span>
  @endfor
</div></div>

{{-- ============ НАПРАВЛЕНИЯ ============ --}}
<section class="sec" id="destinations">
  <div class="wrap">
    <div class="sec-head">
      <div class="sec-head__top"><span class="no">№ 01</span><i></i><span class="kicker">Направления</span></div>
      <h2 class="h2" data-reveal>Куда едем <em>в этот раз</em></h2>
    </div>
    <div class="dest">
      <a class="d1" href="{{ route('destinations.show','italy') }}" data-reveal><div class="ph"><img src="{{ img('places/amalfi-2') }}" alt="Амальфитанское побережье" loading="lazy"></div>
        <div class="dest__t"><span>Италия · Амальфи · Тоскана</span><b>Италия</b></div><span class="dest__go"><svg><use href="#i-arrow"/></svg></span></a>
      <a class="d2" href="{{ route('destinations.show','france') }}" data-reveal style="--d:.1s"><div class="ph"><img src="{{ img('places/riviera') }}" alt="Французская Ривьера" loading="lazy"></div>
        <div class="dest__t"><span>Ривьера · Париж · Прованс</span><b>Фран<em>ция</em></b></div><span class="dest__go"><svg><use href="#i-arrow"/></svg></span></a>
      <a class="d3" href="{{ route('destinations.show','greece') }}" data-reveal style="--d:.15s"><div class="ph"><img src="{{ img('places/santorini') }}" alt="Санторини" loading="lazy"></div>
        <div class="dest__t"><span>Санторини · Афины</span><b>Греция</b></div><span class="dest__go"><svg><use href="#i-arrow"/></svg></span></a>
      <div class="d6" data-reveal style="--d:.2s"><p>«Красота не там, куда едешь, а в том, как медленно ты туда добираешься»<small>VIA BELLA</small></p></div>
      <a class="d4" href="{{ route('destinations.show','switzerland') }}" data-reveal><div class="ph"><img src="{{ img('places/alps') }}" alt="Швейцарские Альпы" loading="lazy"></div>
        <div class="dest__t"><span>Альпы · Люцерн · Лаво</span><b>Швей<em>цария</em></b></div><span class="dest__go"><svg><use href="#i-arrow"/></svg></span></a>
      <a class="d5" href="{{ route('destinations.show','austria') }}" data-reveal style="--d:.1s"><div class="ph"><img src="{{ img('places/austria') }}" alt="Гальштат" loading="lazy"></div>
        <div class="dest__t"><span>Гальштат · Вена</span><b>Австрия</b></div><span class="dest__go"><svg><use href="#i-arrow"/></svg></span></a>
      <div class="dest__note" data-reveal><p>Пять стран, двадцать маршрутов и ни одного лишнего перехода. Мы выбираем места, где хочется остаться чуть дольше.</p><a class="link" href="{{ route('map') }}">На карте Европы <svg><use href="#i-arrow"/></svg></a></div>
    </div>
  </div>
</section>

{{-- ============ ПОИСК ============ --}}
<section class="sec sec--tight" style="padding-top:0" id="find">
  <div class="wrap">
    <form class="finder" action="{{ route('tours.index') }}" method="get" data-reveal>
      <div style="display:flex;justify-content:space-between;align-items:baseline;gap:20px;flex-wrap:wrap"><h2 class="h3" style="font-size:clamp(1.8rem,3vw,2.6rem);font-weight:300">Найдите <em>своё</em> путешествие</h2><span class="kicker">Подбор по каталогу</span></div>
      <div class="finder__grid">
        <div class="field"><label for="f-q">Куда</label>
          <select id="f-q" name="country"><option value="">Вся Европа</option>@foreach(config('via.countries') as $c=>$d)<option value="{{ $c }}">{{ $d['name'] }}</option>@endforeach</select></div>
        <div class="field"><label for="f-d">Когда</label><input id="f-d" type="date" name="date" min="{{ now()->toDateString() }}"></div>
        <div class="field"><label>Путешественники</label>
          <div class="stepper" data-stepper data-min="1" data-max="12"><button type="button" data-d="-" aria-label="Меньше">−</button><output>2</output><input type="hidden" name="guests" value="2"><button type="button" data-d="+" aria-label="Больше">+</button></div></div>
        <div class="field"><label for="f-c">Тип отдыха</label>
          <select id="f-c" name="category"><option value="">Любой</option>@foreach(config('via.categories') as $k=>$c)<option value="{{ $k }}">{{ $c['name'] }}</option>@endforeach</select></div>
        <button class="btn btn--solid finder__go" type="submit">Найти <svg><use href="#i-arrow"/></svg></button>
      </div>
    </form>
  </div>
</section>

{{-- ============ КАТЕГОРИИ ============ --}}
<section class="sec sec--linen">
  <div class="wrap">
    <div class="sec-head sec-head--row">
      <div><div class="sec-head__top" style="margin-bottom:18px"><span class="no">№ 02</span><span class="kicker">Настроение поездки</span></div><h2 class="h2" data-reveal>Найдите путешествие <em>по настроению</em></h2></div>
      <a class="link" href="{{ route('tours.index') }}">Весь каталог <svg><use href="#i-arrow"/></svg></a>
    </div>
    <div class="cats">
      @foreach(array_keys(config('via.categories')) as $i => $k)
        @php $c = config("via.categories.$k"); $words = explode(' ', $c['name'], 2); @endphp
        <a class="cat c{{ $i+1 }}" href="{{ route('tours.index',['category'=>$k]) }}" data-reveal style="--d:{{ $i*.06 }}s">
          <div class="ph"><img src="{{ img($c['img']) }}" alt="{{ $c['name'] }}" loading="lazy"></div>
          @if($i===2)<span class="tag">Новое в выпуске</span>@endif
          <div class="cat__t"><small>0{{ $i+1 }}</small><b>{{ $words[0] }}@if(isset($words[1])) <em>{{ $words[1] }}</em>@endif</b><span>{{ $c['blurb'] }}</span></div>
        </a>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ РЕКОМЕНДОВАННЫЕ ТУРЫ ============ --}}
<section class="sec">
  <div class="wrap">
    <div class="sec-head sec-head--row">
      <div><div class="sec-head__top" style="margin-bottom:18px"><span class="no">№ 03</span><span class="kicker">Подборка редакции</span></div><h2 class="h2" data-reveal>Туры, которые <em>мы любим</em></h2></div>
      <p class="muted" style="max-width:24em">Маршруты, собранные нашими гидами лично. Небольшие группы, лучшие места и время для себя.</p>
    </div>
    <div class="picks">
      @php $f = $featured->values(); @endphp
      <div class="pcol pcol-a">
        @if($f->get(0))<div class="p1 big">@include('partials.tour-card',['tour'=>$f[0],'delay'=>0,'showSub'=>true,'label'=>'Выбор редакции'])</div>@endif
        @if($f->get(3))<div class="p4">@include('partials.tour-card',['tour'=>$f[3],'delay'=>0])</div>@endif
      </div>
      <div class="pcol pcol-b">
        @if($f->get(1))<div class="p2">@include('partials.tour-card',['tour'=>$f[1],'delay'=>.1,'label'=>'Бестселлер','ratio'=>'4/5'])</div>@endif
        <div class="pickquote" data-reveal>«Мы не продаём места в автобусе. Мы собираем дни, которые хочется пересматривать.»<small>Редакция VIA BELLA</small></div>
      </div>
      <div class="pcol pcol-c">
        @if($f->get(2))<div class="p3">@include('partials.tour-card',['tour'=>$f[2],'delay'=>.15,'showSub'=>true])</div>@endif
        @if($f->get(4))<div class="p5">@include('partials.tour-card',['tour'=>$f[4],'delay'=>.1,'label'=>'Новинка'])</div>@endif
      </div>
    </div>
    <div style="text-align:center;margin-top:clamp(40px,6vw,80px)"><a class="btn" href="{{ route('tours.index') }}">Смотреть все туры <svg><use href="#i-arrow"/></svg></a></div>
  </div>
</section>

@if($recommended->isNotEmpty())
<section class="sec sec--pist">
  <div class="wrap">
    <div class="sec-head sec-head--row"><div><div class="sec-head__top" style="margin-bottom:18px"><span class="kicker">Для вас, {{ auth()->user()->name }}</span></div><h2 class="h2">Подобрано <em>по вашим интересам</em></h2></div><a class="link" href="{{ route('account',['tab'=>'profile']) }}">Изменить интересы <svg><use href="#i-arrow"/></svg></a></div>
    <div class="grid-cards" style="grid-template-columns:repeat(3,1fr)">@foreach($recommended as $t)@include('partials.tour-card',['tour'=>$t])@endforeach</div>
  </div>
</section>
@endif

{{-- ============ ПЛАНИРОВЩИК ============ --}}
<section class="sec sec--tight">
  <div class="wrap">
    <div class="planner-teaser">
      <div class="collage" data-reveal>
        <div class="ph" style="left:0;top:0;width:58%;aspect-ratio:4/5"><img src="{{ img('places/lucerne') }}" alt="Люцерн" loading="lazy"></div>
        <div class="ph" style="right:0;top:12%;width:42%;aspect-ratio:3/4;border:8px solid var(--cream)"><img src="{{ img('places/como') }}" alt="Озеро Комо" loading="lazy"></div>
        <div class="ph" style="left:30%;bottom:0;width:36%;aspect-ratio:1;border:8px solid var(--cream)"><img src="{{ img('places/cafe') }}" alt="Кофейня" loading="lazy"></div>
      </div>
      <div data-reveal style="--d:.1s">
        <div class="sec-head__top" style="margin-bottom:18px"><span class="no">№ 04</span><span class="kicker">Планировщик</span></div>
        <h2 class="h2">Соберите путешествие <em>сами</em></h2>
        <p class="lead" style="margin-top:22px">Выберите страну, перелёт, проживание и впечатления — и сразу увидите маршрут и общую стоимость. Исследуйте Европу на интерактивной карте.</p>
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:28px"><a class="btn btn--solid" href="{{ route('builder') }}">Конструктор <svg><use href="#i-arrow"/></svg></a><a class="btn" href="{{ route('map') }}">Карта Европы</a></div>
        <p class="muted" style="font-size:13px;margin-top:20px">Авиабилеты и отели — демонстрационные данные, без реальной продажи.</p>
      </div>
    </div>
  </div>
</section>

{{-- ============ ГИДЫ ============ --}}
<section class="sec sec--linen">
  <div class="wrap">
    <div class="sec-head sec-head--row">
      <div><div class="sec-head__top" style="margin-bottom:18px"><span class="no">№ 05</span><span class="kicker">Люди VIA BELLA</span></div><h2 class="h2" data-reveal>Люди, которые <em>открывают города</em></h2></div>
      <a class="link" href="{{ route('guides.index') }}">Все гиды <svg><use href="#i-arrow"/></svg></a>
    </div>
    <div class="guides">
      @foreach($guides as $i=>$g)
      <a class="guide" href="{{ route('guides.show',$g) }}" data-reveal style="--d:{{ $i*.08 }}s">
        <div class="ph guide__ph"><img src="{{ $g->photoUrl() }}" alt="{{ $g->name }}, гид" loading="lazy"></div>
        <div class="guide__b"><small>{{ config("via.countries.{$g->country}.name") }} · {{ $g->city }}</small><b>{{ $g->name }}</b><span>{{ $g->specialization }}</span></div>
      </a>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ ОТЗЫВЫ ============ --}}
<section class="sec">
  <div class="wrap">
    <div class="sec-head"><div class="sec-head__top"><span class="no">№ 06</span><i></i><span class="kicker">Истории путешественников</span></div><h2 class="h2" data-reveal>Что они <em>рассказывают</em></h2></div>
    <div class="stories">
      @foreach($reviews as $i=>$r)
      <article class="story s{{ $i+1 }}" data-reveal style="--d:{{ $i*.1 }}s">
        <div class="ph story__ph"><img src="{{ img($r->photo ?: $r->tour->cover) }}" alt="{{ $r->tour->title }}" loading="lazy"><span class="cap">{{ $r->tour->location }}</span></div>
        <div class="story__b">
          <blockquote>{{ \Illuminate\Support\Str::limit($r->text, $i===0 ? 190 : 130) }}</blockquote>
          <div class="who">@if($r->user->avatar)<img src="{{ asset($r->user->avatar) }}" alt="{{ $r->user->name }}" loading="lazy">@else<span class="ava">{{ $r->user->initials() }}</span>@endif
            <div><b>{{ $r->user->name }}</b><span>{{ $r->user->city }} · {{ $r->tour->title }}</span></div>
            <span style="margin-left:auto">@include('partials.stars',['value'=>$r->rating])</span></div>
        </div>
      </article>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ ВДОХНОВЕНИЕ ============ --}}
<section class="sec sec--pist">
  <div class="wrap">
    <div class="sec-head sec-head--row"><div><div class="sec-head__top" style="margin-bottom:18px"><span class="no">№ 07</span><span class="kicker">Вдохновение</span></div><h2 class="h2" data-reveal>Страницы <em>журнала</em></h2></div><a class="link" href="{{ route('journal') }}">Читать все <svg><use href="#i-arrow"/></svg></a></div>
    <div class="insp">
      @foreach(array_slice(config('via.journal'),0,5) as $i=>$j)
      <a class="ins {{ $j['size'] }}" href="{{ route('journal') }}#{{ $j['slug'] }}" data-reveal style="--d:{{ $i*.07 }}s"><div class="ph"><img src="{{ img($j['img']) }}" alt="{{ $j['title'] }}" loading="lazy"></div><span class="kicker">{{ $j['kicker'] }}</span><h3>{{ $j['title'] }}</h3></a>
      @endforeach
    </div>
  </div>
</section>

<section class="sec" style="text-align:center">
  <div class="wrap" data-reveal>
    <svg width="44" height="44" style="color:var(--coral-d)"><use href="#i-sun"/></svg>
    <h2 class="display" style="margin:18px auto 22px;max-width:12em">Путешествуй <em>красиво</em>. Создавай <em>незабываемое</em>.</h2>
    <p class="kicker" style="margin-bottom:34px">Travel beautifully. Live unforgettable.</p>
    <a class="btn btn--solid" href="{{ route('tours.index') }}">Выбрать путешествие <svg><use href="#i-arrow"/></svg></a>
  </div>
</section>
@endsection
