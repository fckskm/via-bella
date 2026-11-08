@extends('layouts.app')
@section('title','Бронирование — '.$tour->title)
@section('body-class','page-plain')
@section('content')
@php
  $u = auth()->user();
  $errStep = $errors->has('schedule_id') ? 0 : ($errors->has('participants_count') ? 1 : ($errors->hasAny(['contact_name','contact_email','contact_phone','comment']) ? 2 : ($errors->has('agree') ? 4 : 0)));
@endphp
<div class="wrap" style="padding-block:clamp(30px,5vw,70px) clamp(70px,9vw,130px)">
  <div class="crumbs"><a href="{{ route('tours.index') }}">Каталог</a><i>/</i><a href="{{ route('tours.show',$tour) }}">{{ $tour->title }}</a><i>/</i><span>Бронирование</span></div>
  <h1 class="h2" style="margin-bottom:clamp(26px,4vw,50px)">Бронирование <em>вашего путешествия</em></h1>

  <ol class="progress" aria-label="Шаги бронирования" style="padding:0">
    <li class="cur"><span>Дата</span></li><li><span>Участники</span></li><li><span>Контакты</span></li><li><span>Проверка</span></li><li><span>Подтверждение</span></li>
  </ol>

  <div class="bk">
    <form id="bkForm" method="post" action="{{ route('booking.store',$tour) }}" data-price="{{ (int)$tour->price }}" data-errstep="{{ $errors->any() ? $errStep : 0 }}" novalidate>
      @csrf
      @if($errors->any())<div class="alert alert--err" style="margin-bottom:24px">Не удалось оформить заявку: {{ $errors->first() }}</div>@endif

      <section class="step on">
        <span class="kicker">Шаг 1</span><h2 class="h2">Выберите <em>дату</em></h2>
        <p class="muted" style="margin:10px 0 26px">Свободные места проверяются сервером в момент оформления.</p>
        <div class="dates">
          @forelse($schedules as $s)
            @php $f = $s->free; @endphp
            <label class="date-opt {{ $f===0?'full':'' }} {{ $f>0&&$f<=3?'low':'' }} {{ ($preselected===$s->id || old('schedule_id')==$s->id)?'on':'' }}">
              <input class="sr" type="radio" name="schedule_id" value="{{ $s->id }}" data-free="{{ $f }}" data-label="{{ ru_date($s->starts_at,'j F Y') }}@if($tour->duration>1) — {{ ru_date($s->ends_at,'j F') }}@endif" @checked($preselected===$s->id || old('schedule_id')==$s->id) @disabled($f===0)>
              <span><b>{{ ru_date($s->starts_at,'j F Y') }}@if($tour->duration>1) — {{ ru_date($s->ends_at,'j F') }}@endif</b><div class="bar"><i style="width:{{ 100-($f/$s->capacity*100) }}%"></i></div></span>
              <small>{{ $f===0?'Мест нет':'Свободно '.$f }}<br>из {{ $s->capacity }}</small>
            </label>
          @empty <p>Нет доступных дат.</p> @endforelse
        </div>
        <p class="err" id="dateErr">@error('schedule_id'){{ $message }}@enderror</p>
        <div class="nav"><a class="link" href="{{ route('tours.show',$tour) }}">← К туру</a><button type="button" class="btn btn--solid" data-next>Далее <svg><use href="#i-arrow"/></svg></button></div>
      </section>

      <section class="step">
        <span class="kicker">Шаг 2</span><h2 class="h2">Сколько <em>вас</em></h2>
        <p class="muted" id="freeHint" style="margin:10px 0 30px"></p>
        <div class="counter"><button type="button" data-cd="-" aria-label="Меньше">−</button><output id="pcountOut">{{ old('participants_count',$guests) }}</output><button type="button" data-cd="+" aria-label="Больше">+</button></div>
        <input type="hidden" name="participants_count" id="pcount" value="{{ old('participants_count',$guests) }}">
        <p class="err" id="cntErr">@error('participants_count'){{ $message }}@enderror</p>
        <div class="nav"><button type="button" class="link" data-prev style="background:none;border-width:0 0 1px">← Назад</button><button type="button" class="btn btn--solid" data-next>Далее <svg><use href="#i-arrow"/></svg></button></div>
      </section>

      <section class="step">
        <span class="kicker">Шаг 3</span><h2 class="h2">Ваши <em>контакты</em></h2>
        <div class="form" style="margin-top:26px">
          <div class="fg"><label for="cn">Имя и фамилия</label><input class="in serif" id="cn" name="contact_name" value="{{ old('contact_name',$u->name) }}" required autocomplete="name">@error('contact_name')<p class="err">{{ $message }}</p>@enderror</div>
          <div class="row2"><div class="fg"><label for="ce">Электронная почта</label><input class="in serif" id="ce" type="email" name="contact_email" value="{{ old('contact_email',$u->email) }}" required autocomplete="email">@error('contact_email')<p class="err">{{ $message }}</p>@enderror</div>
          <div class="fg"><label for="cp">Телефон</label><input class="in serif" id="cp" type="tel" name="contact_phone" value="{{ old('contact_phone',$u->phone) }}" required autocomplete="tel" placeholder="+7 …">@error('contact_phone')<p class="err">{{ $message }}</p>@enderror</div></div>
          <div class="fg"><label for="cc">Пожелания (необязательно)</label><textarea class="in" id="cc" name="comment" maxlength="1000" placeholder="Аллергии, пожелания к размещению…">{{ old('comment') }}</textarea></div>
        </div>
        <div class="nav"><button type="button" class="link" data-prev style="background:none;border-width:0 0 1px">← Назад</button><button type="button" class="btn btn--solid" data-next>Проверить <svg><use href="#i-arrow"/></svg></button></div>
      </section>

      <section class="step">
        <span class="kicker">Шаг 4</span><h2 class="h2">Проверьте <em>данные</em></h2>
        <div class="review-box" style="margin-top:28px"><dl>
          <dt>Тур</dt><dd>{{ $tour->title }}</dd><dt>Дата</dt><dd data-r="date"></dd><dt>Участников</dt><dd data-r="count"></dd>
          <dt>Имя</dt><dd data-r="name"></dd><dt>Почта</dt><dd data-r="email"></dd><dt>Телефон</dt><dd data-r="phone"></dd><dt>Итого</dt><dd data-r="total" style="font-size:2rem"></dd>
        </dl></div>
        <div class="nav"><button type="button" class="link" data-prev style="background:none;border-width:0 0 1px">← Изменить</button><button type="button" class="btn btn--solid" data-next>Всё верно <svg><use href="#i-arrow"/></svg></button></div>
      </section>

      <section class="step">
        <span class="kicker">Шаг 5</span><h2 class="h2">Отправить <em>заявку</em></h2>
        <p class="lead" style="margin:14px 0 26px">Мы забронируем места за вами и свяжемся для подтверждения. Деньги сейчас не списываются — это демонстрационный проект.</p>
        <label class="check"><input type="checkbox" name="agree" id="agree" value="1" @checked(old('agree'))><span>Я ознакомился(-ась) с условиями: заявка обрабатывается администратором, оплата не производится, бесплатная отмена до начала поездки.</span></label>
        <p class="err" id="agreeErr">@error('agree'){{ $message }}@enderror</p>
        <div class="nav"><button type="button" class="link" data-prev style="background:none;border-width:0 0 1px">← Назад</button><button type="submit" class="btn btn--coral" id="submitBtn">Забронировать <svg><use href="#i-arrow"/></svg></button></div>
      </section>
    </form>

    <aside class="summary" data-reveal>
      <div class="ph"><img src="{{ img($tour->cover) }}" alt="{{ $tour->title }}"></div>
      <div class="summary__b">
        <span class="kicker">{{ $tour->countryName() }} · {{ $tour->location }}</span>
        <h3>{{ $tour->title }}</h3>
        <div><div class="line"><span>Дата</span><b data-sum-date>Дата не выбрана</b></div>
        <div class="line"><span>Участников</span><b data-sum-count>{{ $guests }}</b></div>
        <div class="line"><span>Стоимость за человека</span><b>{{ eur($tour->price) }}</b></div></div>
        <div class="total"><span class="lbl">Итого</span><b data-sum-total>{{ eur($tour->price*$guests) }}</b></div>
        <div class="demo-note"><svg width="18" height="18" style="flex:none"><use href="#i-check"/></svg><span>Это заявка, а не оплата. Подтверждение — после проверки администратором.</span></div>
      </div>
    </aside>
  </div>
</div>
@endsection
