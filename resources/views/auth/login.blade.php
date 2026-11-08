@extends('layouts.app')
@section('title','Вход — VIA BELLA')
@section('body-class','page-plain')
@section('no-footer','1')
@section('content')
<div class="auth">
  <div class="auth__ph"><div class="ph"><img src="{{ img('places/lucerne') }}" alt="Люцерн"></div><blockquote>Добро пожаловать обратно. Следующая страница журнала — ваша.</blockquote></div>
  <div class="auth__f">
    <span class="kicker">Личный кабинет</span><h1 class="h2" style="margin:12px 0 30px">Войти</h1>
    @if(session('status'))<div class="alert" style="margin-bottom:20px">{{ session('status') }}</div>@endif
    <form class="form" method="post" action="{{ route('login') }}">@csrf
      <div class="fg"><label for="email">Электронная почта</label><input class="in serif @error('email') bad @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">@error('email')<p class="err">{{ $message }}</p>@enderror</div>
      <div class="fg"><label for="password">Пароль</label><input class="in serif" id="password" type="password" name="password" required autocomplete="current-password">@error('password')<p class="err">{{ $message }}</p>@enderror</div>
      <label class="check"><input type="checkbox" name="remember"><span>Запомнить меня</span></label>
      <div style="display:flex;gap:18px;align-items:center;flex-wrap:wrap"><button class="btn btn--solid" type="submit">Войти <svg><use href="#i-arrow"/></svg></button><a class="link" href="{{ route('register') }}">Создать аккаунт</a></div>
    </form>
    <p class="muted" style="font-size:12.5px;margin-top:34px">Демо-доступ: demo@viabella.test / Demo12345</p>
  </div>
</div>
@endsection
