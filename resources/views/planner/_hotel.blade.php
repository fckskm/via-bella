<article class="hotel" data-reveal>
  <a class="ph" href="{{ route('destinations.show',$h['country']) }}"><img src="{{ img($h['img']) }}" alt="{{ $h['name'] }}" loading="lazy"></a>
  <div>
    <span class="kicker">{{ $h['city'] }} · {{ config("via.countries.{$h['country']}.name") }} · {{ str_repeat('★',$h['stars']) }}</span>
    <h3 style="margin:8px 0">{{ $h['name'] }}</h3>
    <p class="muted" style="margin:0 0 12px">{{ $h['blurb'] }}</p>
    <div class="pill-grid">@foreach($h['tags'] as $t)<span class="chip chip--soft" style="font-size:11px">{{ $t }}</span>@endforeach</div>
  </div>
  <div style="text-align:right;display:grid;gap:10px;justify-items:end">
    <span class="price"><small>от</small>{{ eur($h['price']) }}<small style="margin:0 0 0 6px">/ ночь</small></span>
    <span class="stars"><svg><use href="#i-star"/></svg>{{ $h['rating'] }}</span>
    <button class="btn btn--sm" data-fav="hotel:{{ $h['slug'] }}" aria-pressed="false">В избранное</button>
  </div>
</article>
