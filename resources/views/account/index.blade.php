@extends('layouts.app')
@section('title','Личный кабинет — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap" style="padding-block:clamp(36px,6vw,90px) clamp(60px,9vw,130px)">
  <header style="display:flex;gap:24px;align-items:center;justify-content:space-between;flex-wrap:wrap;margin-bottom:30px">
    <div style="display:flex;gap:22px;align-items:center">
      <span class="ava" style="width:78px;height:78px;font-size:1.9rem">{{ $user->initials() }}</span>
      <div><span class="kicker">Личный кабинет</span><h1 class="h2" style="margin-top:8px">{{ $user->name }}</h1></div>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap">
      @if($user->isAdmin())<a class="btn btn--sm btn--solid" href="{{ route('admin.dashboard') }}">Панель администратора</a>@endif
      <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn--sm" type="submit">Выйти</button></form>
    </div>
  </header>
  @if(session('status'))<div class="alert" style="margin-bottom:20px">{{ session('status') }}</div>@endif
  <nav class="tabs">
    @foreach(['bookings'=>'Бронирования','history'=>'История путешествий','favorites'=>'Избранное','profile'=>'Профиль и интересы'] as $k=>$l)<a class="{{ $tab===$k?'on':'' }}" href="{{ route('account',['tab'=>$k]) }}">{{ $l }}@if($k==='bookings' && $upcoming->count())<span class="badge" style="position:static;display:inline-block;margin-left:6px">{{ $upcoming->count() }}</span>@endif</a>@endforeach
  </nav>

  @if($tab==='bookings')
    @forelse($upcoming as $b) @include('account._booking',['b'=>$b]) @empty
      <div class="empty"><h3>Предстоящих поездок нет</h3><p class="muted">Выберите путешествие — и оно появится здесь.</p><a class="btn" href="{{ route('tours.index') }}">Открыть каталог</a></div>
    @endforelse
    @if($cancelled->count())<h3 class="h3" style="margin:40px 0 4px">Отменённые</h3>@foreach($cancelled as $b)@include('account._booking',['b'=>$b])@endforeach @endif
    @if($recommended->count())
      <div class="sec-head__top" style="margin:clamp(50px,7vw,90px) 0 26px"><span class="kicker">Рекомендации для вас</span><i></i></div>
      <div class="grid-cards">@foreach($recommended as $t)@include('partials.tour-card',['tour'=>$t])@endforeach</div>
    @endif
  @elseif($tab==='history')
    @forelse($history as $b) @include('account._booking',['b'=>$b,'review'=>true]) @empty
      <div class="empty"><h3>История пока пуста</h3><p class="muted">Здесь появятся завершённые поездки — и можно будет оставить отзыв.</p></div>
    @endforelse
  @elseif($tab==='favorites')
    <div id="favBox" data-catalog="{{ route('tours.index') }}"></div>
  @else
    <div class="row2" style="gap:clamp(30px,5vw,80px)">
      <form class="form" method="post" action="{{ route('account.update') }}">@csrf @method('PUT')
        <h2 class="h3">Профиль</h2>
        <div class="fg"><label for="n">Имя</label><input class="in serif" id="n" name="name" value="{{ old('name',$user->name) }}" required>@error('name')<p class="err">{{ $message }}</p>@enderror</div>
        <div class="fg"><label for="e">Почта</label><input class="in serif" id="e" type="email" name="email" value="{{ old('email',$user->email) }}" required>@error('email')<p class="err">{{ $message }}</p>@enderror</div>
        <div class="row2"><div class="fg"><label for="p">Телефон</label><input class="in serif" id="p" name="phone" value="{{ old('phone',$user->phone) }}">@error('phone')<p class="err">{{ $message }}</p>@enderror</div><div class="fg"><label for="c">Город</label><input class="in serif" id="c" name="city" value="{{ old('city',$user->city) }}"></div></div>
        <div class="fg"><label>Интересы (для рекомендаций)</label><div class="pill-grid">@foreach($interests as $k=>$i)<label><input type="checkbox" name="interests[]" value="{{ $k }}" @checked(in_array($k,old('interests',$user->interests ?? [])))><span class="chip">{{ $i['name'] }}</span></label>@endforeach</div></div>
        <div><button class="btn btn--solid" type="submit">Сохранить</button></div>
      </form>
      <form class="form" method="post" action="{{ route('account.password') }}">@csrf @method('PUT')
        <h2 class="h3">Смена пароля</h2>
        <div class="fg"><label for="cp">Текущий пароль</label><input class="in serif" id="cp" type="password" name="current_password" required autocomplete="current-password">@error('current_password')<p class="err">{{ $message }}</p>@enderror</div>
        <div class="fg"><label for="np">Новый пароль</label><input class="in serif" id="np" type="password" name="password" required autocomplete="new-password">@error('password')<p class="err">{{ $message }}</p>@enderror</div>
        <div class="fg"><label for="npc">Повторите пароль</label><input class="in serif" id="npc" type="password" name="password_confirmation" required autocomplete="new-password"></div>
        <div><button class="btn" type="submit">Изменить пароль</button></div>
      </form>
    </div>
  @endif
</div>
@endsection
