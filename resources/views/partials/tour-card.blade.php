@php
  $ratio = $ratio ?? '';
  $label = $label ?? ($tour->kind === 'excursion' ? 'Экскурсия' : (config('via.categories.'.$tour->category.'.name')));
@endphp
<article class="card {{ $class ?? '' }}" @isset($delay) data-reveal style="--d:{{ $delay }}s" @endisset>
  <a href="{{ route('tours.show', $tour) }}" aria-label="{{ $tour->title }}">
    <div class="ph card__ph" @if($ratio) style="aspect-ratio:{{ $ratio }}" @endif>
      <img src="{{ img($tour->cover) }}" alt="{{ $tour->title }} — {{ $tour->location }}" loading="lazy">
      <span class="plash {{ $tour->accent }}">{{ $label }}</span>
    </div>
  </a>
  <button class="fav" data-fav="tour:{{ $tour->slug }}" aria-label="Добавить в избранное" aria-pressed="false"><svg><use href="#i-heart"/></svg></button>
  <div class="card__body">
    <div class="meta"><span>{{ $tour->countryName() }}</span><span>{{ $tour->durationText() }}</span></div>
    <h3><a href="{{ route('tours.show', $tour) }}">{{ $tour->title }}</a></h3>
    @if(!empty($showSub) && $tour->tagline)<p class="sub">{{ $tour->tagline }}</p>@endif
    <div class="card__foot">
      <span class="price"><small>от</small>{{ eur($tour->price) }}</span>
      @include('partials.stars', ['value' => $tour->rating_avg ?? null, 'count' => $tour->reviews_count ?? null])
    </div>
  </div>
</article>
