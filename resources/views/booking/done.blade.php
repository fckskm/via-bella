@extends('layouts.app')
@section('title','Заявка принята — VIA BELLA')
@section('body-class','page-plain')
@section('content')
@php $t = $booking->schedule->tour; @endphp
<div class="wrap" style="padding-block:clamp(40px,7vw,110px)">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,80px);align-items:center" class="done">
    <div class="ph" style="aspect-ratio:4/5" data-reveal><img src="{{ img($t->cover) }}" alt="{{ $t->title }}"></div>
    <div data-reveal style="--d:.1s">
      <svg width="40" height="40" style="color:var(--coral-d)"><use href="#i-sun"/></svg>
      <span class="kicker" style="display:block;margin:14px 0">Заявка № {{ $booking->reference }}</span>
      <h1 class="h2">Места <em>закреплены</em> за вами</h1>
      <p class="lead" style="margin:20px 0 26px">Мы сохранили заявку и свяжемся с вами по адресу {{ $booking->contact_email }} для подтверждения. Статус: «{{ $booking->statusLabel() }}». Оплата не производилась.</p>
      <div class="review-box"><dl>
        <dt>Тур</dt><dd>{{ $t->title }}</dd>
        <dt>Дата</dt><dd>{{ ru_date($booking->schedule->starts_at,'j F Y') }}</dd>
        <dt>Участников</dt><dd>{{ $booking->participants_count }}</dd>
        <dt>Итого</dt><dd>{{ eur($booking->total_price) }}</dd>
      </dl></div>
      <div style="display:flex;gap:14px;margin-top:34px;flex-wrap:wrap"><a class="btn btn--solid" href="{{ route('account') }}">Мои бронирования</a><a class="btn" href="{{ route('tours.index') }}">Продолжить выбор</a></div>
    </div>
  </div>
</div>
<style>@media(max-width:760px){.done{grid-template-columns:1fr!important}}</style>
@endsection
