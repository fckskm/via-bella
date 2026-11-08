@php
  $countries = config('via.countries');
  $cats = config('via.categories');
  $themes = config('via.themes');
  $cur = request()->routeIs('tours.*') ? 'tours' : (request()->routeIs('guides.*') ? 'guides' : null);
@endphp
<header class="hdr" id="hdr">
  <div class="hdr__in">
    <nav class="hdr__l" aria-label="Основная навигация">
      <button class="icon-btn burger" id="burger" aria-label="Меню" aria-expanded="false" aria-controls="drawer"><svg><use href="#i-menu"/></svg></button>
      <div class="nav-desktop">
        <div class="nav-item">
          <a class="nav-link" href="{{ route('destinations') }}">Путешествия</a>
          <div class="mega">
            <div><h4>Страны</h4><ul>@foreach($countries as $code=>$c)<li><a href="{{ route('destinations.show',$code) }}">{{ $c['name'] }}</a></li>@endforeach</ul></div>
            <div><h4>Города и места</h4><ul>
              <li><a href="{{ route('destinations.show','italy') }}">Амальфи <small>Италия</small></a></li>
              <li><a href="{{ route('destinations.show','france') }}">Париж <small>Франция</small></a></li>
              <li><a href="{{ route('destinations.show','greece') }}">Санторини <small>Греция</small></a></li>
              <li><a href="{{ route('destinations.show','switzerland') }}">Люцерн <small>Швейцария</small></a></li>
              <li><a href="{{ route('destinations.show','austria') }}">Гальштат <small>Австрия</small></a></li>
              <li><a href="{{ route('map') }}"><em>Все на карте →</em></a></li>
            </ul></div>
            <div><h4>Виды отдыха</h4><ul>@foreach($cats as $k=>$c)<li><a href="{{ route('tours.index',['category'=>$k,'kind'=>'tour']) }}">{{ $c['name'] }}</a></li>@endforeach</ul></div>
            <a class="mega__photo ph" href="{{ route('destinations.show','italy') }}"><img src="{{ img('places/amalfi-2') }}" alt="Побережье Амальфи" loading="lazy"><span>Амальфи — идеальная точка для первого путешествия</span></a>
          </div>
        </div>
        <div class="nav-item">
          <a class="nav-link {{ $cur==='tours' && request('kind')==='excursion' ? 'active' : '' }}" href="{{ route('tours.index',['kind'=>'excursion']) }}">Экскурсии</a>
          <div class="mega mega--sm">
            <div><h4>Впечатления</h4><ul>@foreach($themes as $k=>$t)<li><a href="{{ route('tours.index',['kind'=>'excursion','theme'=>$k]) }}">{{ $t }}</a></li>@endforeach</ul></div>
            <div><h4>Форматы</h4><ul>
              <li><a href="{{ route('tours.index',['kind'=>'excursion']) }}">Все экскурсии</a></li>
              <li><a href="{{ route('tours.index',['kind'=>'tour']) }}">Многодневные туры</a></li>
              <li><a href="{{ route('guides.index') }}">Авторы и гиды</a></li>
              <li><a href="{{ route('journal') }}">Вдохновение</a></li>
            </ul></div>
            <a class="mega__photo ph" href="{{ route('tours.index',['kind'=>'excursion','theme'=>'gastronomic']) }}"><img src="{{ img('places/wine') }}" alt="Гастрономия" loading="lazy"><span>Ужин за закрытыми дверями: Рим, Флоренция, Прованс</span></a>
          </div>
        </div>
        <div class="nav-item">
          <a class="nav-link {{ $cur==='guides' ? 'active':'' }}" href="{{ route('guides.index') }}">Гиды</a>
        </div>
        <div class="nav-item">
          <a class="nav-link" href="{{ route('builder') }}">Планировщик</a>
          <div class="mega mega--sm">
            <div><h4>Соберите сами</h4><ul>
              <li><a href="{{ route('builder') }}">Конструктор путешествия</a></li>
              <li><a href="{{ route('flights') }}">Авиабилеты <small>демо</small></a></li>
              <li><a href="{{ route('hotels') }}">Отели <small>демо</small></a></li>
              <li><a href="{{ route('map') }}">Карта Европы</a></li>
            </ul></div>
            <div><h4>Журнал</h4><ul>
              <li><a href="{{ route('journal') }}">Вдохновение</a></li>
              <li><a href="{{ route('about') }}">О VIA BELLA</a></li>
            </ul></div>
            <a class="mega__photo ph" href="{{ route('map') }}"><img src="{{ img('places/lucerne') }}" alt="Люцерн" loading="lazy"><span>Интерактивная карта — выберите точку и узнайте больше</span></a>
          </div>
        </div>
      </div>
    </nav>
    @include('partials.logo')
    <div class="hdr__r">
      <button class="icon-btn" id="searchOpen" aria-label="Поиск"><svg><use href="#i-search"/></svg></button>
      <a class="icon-btn" href="{{ route('favorites') }}" aria-label="Избранное"><svg><use href="#i-heart"/></svg><span class="badge" id="favCount" data-n="0"></span></a>
      @auth
        <a class="icon-btn" href="{{ route('account') }}" aria-label="Личный кабинет"><svg><use href="#i-user"/></svg></a>
      @else
        <a class="icon-btn" href="{{ route('login') }}" aria-label="Войти"><svg><use href="#i-user"/></svg></a>
      @endauth
    </div>
  </div>
</header>

<div class="drawer" id="drawer" aria-hidden="true">
  <details><summary>Путешествия</summary><div class="sub">
    @foreach($countries as $code=>$c)<a href="{{ route('destinations.show',$code) }}">{{ $c['name'] }}</a>@endforeach
    <a href="{{ route('map') }}">Карта Европы</a></div></details>
  <details><summary>Экскурсии</summary><div class="sub">
    @foreach($themes as $k=>$t)<a href="{{ route('tours.index',['kind'=>'excursion','theme'=>$k]) }}">{{ $t }}</a>@endforeach</div></details>
  <details><summary>Виды отдыха</summary><div class="sub">
    @foreach($cats as $k=>$c)<a href="{{ route('tours.index',['category'=>$k]) }}">{{ $c['name'] }}</a>@endforeach</div></details>
  <a href="{{ route('guides.index') }}">Гиды</a>
  <details><summary>Планировщик</summary><div class="sub">
    <a href="{{ route('builder') }}">Конструктор</a><a href="{{ route('flights') }}">Авиабилеты</a><a href="{{ route('hotels') }}">Отели</a></div></details>
  <a href="{{ route('journal') }}">Вдохновение</a>
  <div class="tiny">
    @auth <a class="link" href="{{ route('account') }}">Кабинет</a> @else <a class="link" href="{{ route('login') }}">Войти</a><a class="link" href="{{ route('register') }}">Регистрация</a> @endauth
    <a class="link" href="{{ route('favorites') }}">Избранное</a>
  </div>
</div>

<div class="search-ov" id="searchOv" aria-hidden="true">
  <button class="icon-btn search-ov__close" id="searchClose" aria-label="Закрыть"><svg><use href="#i-close"/></svg></button>
  <form action="{{ route('tours.index') }}" method="get" role="search">
    <svg width="28" height="28"><use href="#i-search"/></svg>
    <input type="search" name="q" placeholder="Куда хочется?" aria-label="Поиск по турам" autocomplete="off" id="searchInput">
    <button class="btn btn--sm" type="submit">Найти</button>
  </form>
  <div class="tags">
    <span class="lbl" style="align-self:center;margin-right:6px">Часто ищут</span>
    @foreach(['Амальфи','Санторини','Париж','Тоскана','Альпы','Вена'] as $t)<a class="chip" href="{{ route('tours.index',['q'=>$t]) }}">{{ $t }}</a>@endforeach
  </div>
</div>
