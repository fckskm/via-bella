@extends('layouts.app')
@section('title','Избранное — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap" style="padding-bottom:clamp(60px,9vw,130px)">
  <header class="page-head"><div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>Избранное</span></div><h1>Ваша <em>коллекция</em></h1>
    <p class="lead" style="margin-top:22px">Туры, гиды, направления и отели, которые вы сохранили. @guest Войдите, чтобы они синхронизировались между устройствами.@endguest</p></header>
  <div id="favBox" data-catalog="{{ route('tours.index') }}"><p class="muted">Загружаем…</p></div>
</div>
@endsection
