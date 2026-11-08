@extends('admin.layout')
@section('admin')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px"><h1 class="h2">Гиды</h1><a class="btn btn--solid btn--sm" href="{{ route('admin.guides.create') }}">Добавить</a></div>
<div class="tbl-wrap"><table class="tbl"><thead><tr><th>Имя</th><th>Страна</th><th>Специализация</th><th>Туров</th><th></th></tr></thead><tbody>
@foreach($guides as $g)<tr><td><b>{{ $g->name }}</b></td><td>{{ config("via.countries.{$g->country}.name") }}</td><td>{{ $g->specialization }}</td><td>{{ $g->tours_count }}</td>
<td style="white-space:nowrap"><a class="link" href="{{ route('admin.guides.edit',$g) }}">Изменить</a><form method="post" action="{{ route('admin.guides.destroy',$g) }}" style="display:inline" onsubmit="return confirm('Удалить гида?')">@csrf @method('DELETE')<button class="link" style="background:none;border-width:0 0 1px;cursor:pointer;margin-left:10px">Удалить</button></form></td></tr>@endforeach
</tbody></table></div>
@endsection
