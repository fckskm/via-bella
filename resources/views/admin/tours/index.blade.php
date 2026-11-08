@extends('admin.layout')
@section('admin')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px"><h1 class="h2">Туры</h1><a class="btn btn--solid btn--sm" href="{{ route('admin.tours.create') }}">Добавить</a></div>
<div class="tbl-wrap"><table class="tbl"><thead><tr><th>Название</th><th>Гид</th><th>Формат</th><th>Цена</th><th>Дат</th><th>Статус</th><th></th></tr></thead><tbody>
@foreach($tours as $t)<tr><td><a href="{{ route('admin.tours.edit',$t) }}"><b>{{ $t->title }}</b></a><br><small class="muted">{{ $t->location }}</small></td><td>{{ $t->guide->name }}</td><td>{{ $t->kind==='tour'?'Тур':'Экскурсия' }}</td><td>{{ eur($t->price) }}</td><td>{{ $t->schedules_count }}</td><td>{{ ['draft'=>'Черновик','published'=>'Опубликован','archived'=>'Архив'][$t->publication_status] }}</td>
<td style="white-space:nowrap"><a class="link" href="{{ route('admin.tours.edit',$t) }}">Изменить</a>
<form method="post" action="{{ route('admin.tours.destroy',$t) }}" style="display:inline" onsubmit="return confirm('Удалить тур? Если есть бронирования, он будет архивирован.')">@csrf @method('DELETE')<button class="link" style="background:none;border-width:0 0 1px;cursor:pointer;margin-left:10px">Удалить</button></form></td></tr>@endforeach
</tbody></table></div>
@endsection
