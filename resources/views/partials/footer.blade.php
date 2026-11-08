<footer class="ftr">
  <div class="wrap">
    <div class="ftr__top">
      <div>
        @include('partials.logo')
        <p class="ftr__tag">Путешествуй красиво. Создавай незабываемое.</p>
      </div>
      <div><h4>Путешествия</h4><ul>
        @foreach(config('via.countries') as $c=>$d)<li><a href="{{ route('destinations.show',$c) }}">{{ $d['name'] }}</a></li>@endforeach
      </ul></div>
      <div><h4>Сервис</h4><ul>
        <li><a href="{{ route('tours.index') }}">Каталог туров</a></li>
        <li><a href="{{ route('guides.index') }}">Гиды</a></li>
        <li><a href="{{ route('builder') }}">Конструктор</a></li>
        <li><a href="{{ route('map') }}">Карта Европы</a></li>
        <li><a href="{{ route('journal') }}">Вдохновение</a></li>
      </ul></div>
      <div><h4>Кабинет</h4><ul>
        @auth<li><a href="{{ route('account') }}">Мои бронирования</a></li><li><a href="{{ route('favorites') }}">Избранное</a></li>
        @else<li><a href="{{ route('login') }}">Войти</a></li><li><a href="{{ route('register') }}">Регистрация</a></li>@endauth
        <li><a href="{{ route('about') }}">О проекте</a></li>
        <li><a href="{{ route('credits') }}">Авторство фотографий</a></li>
      </ul></div>
    </div>
    <div class="ftr__bot">
      <span>© {{ date('Y') }} VIA BELLA · European Travel Collection</span>
      <span>Учебный проект · Финансовый университет, Колледж информатики и программирования · Оплата и подтверждения — демонстрационные</span>
    </div>
  </div>
</footer>
