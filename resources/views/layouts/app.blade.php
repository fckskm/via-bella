<!doctype html>
<html lang="ru" class="no-js">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title', 'VIA BELLA — European Travel Collection')</title>
  <meta name="description" content="@yield('description', 'VIA BELLA — премиальная платформа красивых путешествий по Европе: авторские туры, экскурсии, гиды, бронирование.')">
  <meta name="theme-color" content="#F7F4EE">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Ccircle cx='16' cy='16' r='6' fill='%23EDAA98'/%3E%3C/svg%3E">
  <link rel="preload" href="{{ asset('fonts/cormorant-garamond-cyrillic-300-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="{{ asset('css/via.css') }}?v={{ filemtime(public_path('css/via.css')) }}">
  <script>document.documentElement.classList.replace('no-js','js')</script>
  @stack('head')
</head>
<body class="@yield('body-class')">
@include('partials.icons')
@include('partials.header')
<main id="main">
@yield('content')
</main>
@unless(View::hasSection('no-footer'))
@include('partials.footer')
@endunless
@include('partials.assistant')
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script>window.VB={csrf:@json(csrf_token()),auth:@json(auth()->check()),routes:{toggle:@json(route('favorites.toggle')),merge:@json(route('favorites.merge')),assistant:@json(route('assistant')),cards:@json(route('favorites.cards'))}}</script>
<script src="{{ asset('js/via.js') }}?v={{ filemtime(public_path('js/via.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
