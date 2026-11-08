@php $t = $b->schedule->tour; @endphp
<article class="bk-card">
  <a class="ph" href="{{ route('tours.show',$t) }}"><img src="{{ img($t->cover) }}" alt="{{ $t->title }}" loading="lazy"></a>
  <div>
    <span class="status {{ $b->status }}">{{ $b->statusLabel() }}</span>
    <h3 style="margin:10px 0 6px"><a href="{{ route('tours.show',$t) }}">{{ $t->title }}</a></h3>
    <p class="muted" style="margin:0;font-size:14px">{{ ru_date($b->schedule->starts_at,'j F Y') }} · {{ $b->participants_count }} {{ trans_choice('участник|участника|участников',$b->participants_count) }} · {{ eur($b->total_price) }} · № {{ $b->reference }}</p>
  </div>
  <div style="display:grid;gap:10px;justify-items:end">
    @if($b->isCancellable())
      <form method="post" action="{{ route('booking.cancel',$b) }}" onsubmit="return confirm('Отменить бронирование?')">@csrf<button class="btn btn--sm" type="submit">Отменить</button></form>
    @endif
  </div>
  @if(!empty($review) && $b->isCompleted())
    <div style="grid-column:1 / -1">
    @if($b->review)
      <div class="alert">Ваш отзыв опубликован: «{{ \Illuminate\Support\Str::limit($b->review->text,120) }}» @include('partials.stars',['value'=>$b->review->rating])</div>
    @else
      <details style="border:1px solid var(--line);padding:18px 22px;background:var(--milk)" @if($errors->has('text')||$errors->has('rating')) open @endif><summary style="cursor:pointer;font:600 11px var(--sans);letter-spacing:.2em;text-transform:uppercase">Оставить отзыв</summary>
        <form class="form" method="post" action="{{ route('reviews.store',$b) }}" style="margin-top:20px">@csrf
          <div class="fg"><label>Оценка</label><div class="stars-in">@for($i=5;$i>=1;$i--)<input type="radio" name="rating" id="r{{ $b->id }}-{{ $i }}" value="{{ $i }}" required><label for="r{{ $b->id }}-{{ $i }}" aria-label="{{ $i }}"><svg><use href="#i-star"/></svg></label>@endfor</div>@error('rating')<p class="err">{{ $message }}</p>@enderror</div>
          <div class="fg"><label>Заголовок</label><input class="in" name="title" maxlength="120" placeholder="Например: Утро, которое я не забуду"></div>
          <div class="fg"><label>Ваша история</label><textarea class="in" name="text" required minlength="20" maxlength="2000" placeholder="Что запомнилось больше всего?"></textarea>@error('text')<p class="err">{{ $message }}</p>@enderror</div>
          <div><button class="btn btn--solid btn--sm" type="submit">Опубликовать</button></div>
        </form></details>
    @endif
    </div>
  @endif
</article>
