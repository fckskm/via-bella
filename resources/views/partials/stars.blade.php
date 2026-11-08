@props(['value' => null, 'count' => null])
@if($value)
<span class="stars"><svg><use href="#i-star"/></svg>{{ number_format($value,1,',','') }}@if($count !== null)<em>({{ $count }} {{ trans_choice('отзыв|отзыва|отзывов',$count) }})</em>@endif</span>
@else
<span class="stars"><em>Новый маршрут</em></span>
@endif
