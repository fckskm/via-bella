@extends('layouts.app')
@section('title','Регистрация — VIA BELLA')
@section('body-class','page-plain')
@section('no-footer','1')
@section('content')
<div class="auth">
  <div class="auth__ph"><div class="ph"><img src="{{ img('places/amalfi-2') }}" alt="Амальфи"></div><blockquote>Расскажите, что вам по душе, — и мы подберём маршруты.</blockquote></div>
  <div class="auth__f">
    <span class="kicker">Новый аккаунт</span><h1 class="h2" style="margin:12px 0 30px">Присоединиться</h1>
    <form class="form" method="post" action="{{ route('register') }}">@csrf
      <div class="fg"><label for="name">Имя</label><input class="in serif" id="name" name="name" value="{{ old('name') }}" required autocomplete="name">@error('name')<p class="err">{{ $message }}</p>@enderror</div>
      <div class="fg"><label for="email">Электронная почта</label><input class="in serif" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<p class="err">{{ $message }}</p>@enderror</div>
      <div class="row2"><div class="fg"><label for="password">Пароль</label><input class="in serif" id="password" type="password" name="password" required autocomplete="new-password">@error('password')<p class="err">{{ $message }}</p>@enderror</div>
      <div class="fg"><label for="pc">Повторите пароль</label><input class="in serif" id="pc" type="password" name="password_confirmation" required autocomplete="new-password"></div></div>
      <p class="muted" style="font-size:12.5px;margin:-6px 0 0">Не менее 8 символов, буквы и цифры. Пароль хранится только в виде хеша.</p>
      <div class="fg"><label>Что вам интересно</label><div class="pill-grid">
        @foreach($interests as $k=>$i)<label><input type="checkbox" name="interests[]" value="{{ $k }}" @checked(in_array($k,old('interests',[])))><span class="chip">{{ $i['name'] }}</span></label>@endforeach
      </div></div>
      <div style="display:flex;gap:18px;align-items:center;flex-wrap:wrap"><button class="btn btn--solid" type="submit">Создать аккаунт <svg><use href="#i-arrow"/></svg></button><a class="link" href="{{ route('login') }}">Уже есть аккаунт</a></div>
    </form>
  </div>
</div>
@endsection
