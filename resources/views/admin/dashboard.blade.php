@extends('admin.layout')
@section('admin')
<h1 class="h2" style="margin-bottom:30px">Обзор</h1>
<div class="stat-row">@foreach($stats as $l=>$v)<div class="stat"><b>{{ $v }}</b><span>{{ $l }}</span></div>@endforeach</div>
<h2 class="h3" style="margin-bottom:14px">Последние заявки</h2>
<div class="tbl-wrap"><table class="tbl"><thead><tr><th>№</th><th>Тур</th><th>Клиент</th><th>Мест</th><th>Статус</th></tr></thead><tbody>
@foreach($latest as $b)<tr><td>{{ $b->reference }}</td><td>{{ $b->schedule->tour->title }}</td><td>{{ $b->user->name }}</td><td>{{ $b->participants_count }}</td><td><span class="status {{ $b->status }}">{{ $b->statusLabel() }}</span></td></tr>@endforeach
</tbody></table></div>
@endsection
