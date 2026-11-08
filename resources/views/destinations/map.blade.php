@extends('layouts.app')
@section('title','Карта Европы — VIA BELLA')
@section('body-class','page-plain')
@section('content')
<div class="wrap" style="padding-bottom:clamp(60px,9vw,130px)">
  <header class="page-head"><div class="crumbs"><a href="{{ route('home') }}">Главная</a><i>/</i><span>Карта</span></div>
    <h1>Европа <em>на ладони</em></h1><p class="lead" style="margin-top:22px">Нажмите на точку — откроется место, а ниже появятся туры и экскурсии рядом.</p></header>
  <div class="map-wrap">
    <div class="map" id="euMap" role="application" aria-label="Интерактивная карта Европы">
      <img class="base" src="{{ asset("img/europe-map.png") }}" alt="">
      @foreach($points as $p)
        @php [$x,$y] = \App\Support\MapProjection::xy($p['lat'],$p['lng']); @endphp
        <button class="pin" data-k="{{ $p['key'] }}" style="left:{{ $x }}%;top:{{ $y }}%" aria-label="{{ $p['name'] }}"><span>{{ $p['name'] }}</span></button>
      @endforeach
    </div>
    <aside class="map-card" id="mapCard"><div class="ph"><img alt=""></div><div class="map-card__b">
      <span class="kicker" data-m="country"></span><h3 data-m="name" style="margin:10px 0 12px"></h3><p data-m="text" class="muted"></p>
      <span class="lbl">Туры рядом</span><ul class="map-list" data-m="tours"></ul></div></aside>
  </div>
  <script type="application/json" id="mapData">@json($points)</script>
</div>
@endsection
