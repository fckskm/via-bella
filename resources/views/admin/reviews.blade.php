@extends('admin.layout')
@section('admin')
<h1 class="h2" style="margin-bottom:24px">Отзывы</h1>
<div class="tbl-wrap"><table class="tbl"><thead><tr><th>Автор</th><th>Тур</th><th>Оценка</th><th>Текст</th><th></th></tr></thead><tbody>
@foreach($reviews as $r)<tr><td>{{ $r->user->name }}</td><td>{{ $r->tour->title }}</td><td>{{ $r->rating }}/5</td><td style="max-width:340px">{{ \Illuminate\Support\Str::limit($r->text,120) }}</td>
<td><form method="post" action="{{ route('admin.reviews.destroy',$r) }}" onsubmit="return confirm('Удалить отзыв?')">@csrf @method('DELETE')<button class="link" style="background:none;border-width:0 0 1px;cursor:pointer">Удалить</button></form></td></tr>@endforeach
</tbody></table></div>
<div style="margin-top:22px">{{ $reviews->links() }}</div>
@endsection
