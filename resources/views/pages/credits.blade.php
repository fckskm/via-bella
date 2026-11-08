@extends('layouts.app')
@section('title','Авторство фотографий — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap" style="padding-bottom:clamp(60px,9vw,130px)">
  <header class="page-head"><div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>Авторство</span></div><h1>Авторство <em>фотографий</em></h1>
  <p class="lead" style="margin-top:22px">Фотографии мест — Wikimedia Commons (свободные лицензии), портреты — Unsplash. Ниже указаны авторы и лицензии.</p></header>
  <table class="tbl"><thead><tr><th>Файл</th><th>Автор</th><th>Лицензия</th></tr></thead><tbody>
    @foreach(json_decode(file_get_contents(public_path('img/credits.json')) ?: '[]', true) as $c)
      <tr><td><a href="{{ $c['page'] }}" rel="noopener" target="_blank">{{ $c['title'] }}</a></td><td>{{ $c['author'] }}</td><td>{{ $c['license'] }}</td></tr>
    @endforeach
  </tbody></table>
</div>
@endsection
