@extends('admin.layout')
@section('admin')
<h1 class="h2" style="margin-bottom:20px">Бронирования</h1>
<div class="pill-grid" style="margin-bottom:22px"><a class="chip {{ !$status?'on':'' }}" href="{{ route('admin.bookings') }}">Все</a>@foreach(\App\Models\Booking::STATUSES as $k=>$l)<a class="chip {{ $status===$k?'on':'' }}" href="{{ route('admin.bookings',['status'=>$k]) }}">{{ $l }}</a>@endforeach</div>
<div class="tbl-wrap"><table class="tbl"><thead><tr><th>№</th><th>Тур / дата</th><th>Клиент</th><th>Мест</th><th>Сумма</th><th>Статус</th></tr></thead><tbody>
@foreach($bookings as $b)<tr><td>{{ $b->reference }}</td><td><b>{{ $b->schedule->tour->title }}</b><br><small class="muted">{{ ru_date($b->schedule->starts_at,'j F Y') }}</small></td><td>{{ $b->contact_name }}<br><small class="muted">{{ $b->contact_email }} · {{ $b->contact_phone }}</small></td><td>{{ $b->participants_count }}</td><td>{{ eur($b->total_price) }}</td>
<td><form method="post" action="{{ route('admin.bookings.update',$b) }}" style="display:flex;gap:8px">@csrf @method('PUT')<select class="in" name="status" style="width:150px" onchange="this.form.submit()">@foreach(\App\Models\Booking::STATUSES as $k=>$l)<option value="{{ $k }}" @selected($b->status===$k)>{{ $l }}</option>@endforeach</select></form></td></tr>@endforeach
</tbody></table></div>
<div style="margin-top:22px">{{ $bookings->links() }}</div>
@endsection
