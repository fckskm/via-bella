@extends('admin.layout')
@section('admin')
@php $edit = $tour->exists; @endphp
<h1 class="h2" style="margin-bottom:26px">{{ $edit ? 'Редактирование тура' : 'Новый тур' }}</h1>
<form class="form" method="post" enctype="multipart/form-data" action="{{ $edit ? route('admin.tours.update',$tour) : route('admin.tours.store') }}">@csrf @if($edit)@method('PUT')@endif
  <div class="fg"><label>Название</label><input class="in serif" name="title" value="{{ old('title',$tour->title) }}" required></div>
  <div class="fg"><label>Подзаголовок</label><input class="in" name="tagline" value="{{ old('tagline',$tour->tagline) }}"></div>
  <div class="fg"><label>Описание</label><textarea class="in" name="description" required>{{ old('description',$tour->description) }}</textarea></div>
  <div class="row3">
    <div class="fg"><label>Гид</label><select class="in" name="guide_id">@foreach($guides as $g)<option value="{{ $g->id }}" @selected(old('guide_id',$tour->guide_id)==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
    <div class="fg"><label>Страна</label><select class="in" name="country">@foreach(config('via.countries') as $c=>$d)<option value="{{ $c }}" @selected(old('country',$tour->country)===$c)>{{ $d['name'] }}</option>@endforeach</select></div>
    <div class="fg"><label>Место</label><input class="in" name="location" value="{{ old('location',$tour->location) }}" required></div>
  </div>
  <div class="row3">
    <div class="fg"><label>Формат</label><select class="in" name="kind"><option value="tour" @selected(old('kind',$tour->kind)==='tour')>Тур</option><option value="excursion" @selected(old('kind',$tour->kind)==='excursion')>Экскурсия</option></select></div>
    <div class="fg"><label>Категория</label><select class="in" name="category">@foreach(config('via.categories') as $k=>$c)<option value="{{ $k }}" @selected(old('category',$tour->category)===$k)>{{ $c['name'] }}</option>@endforeach</select></div>
    <div class="fg"><label>Тема экскурсии</label><select class="in" name="theme"><option value="">—</option>@foreach(config('via.themes') as $k=>$t)<option value="{{ $k }}" @selected(old('theme',$tour->theme)===$k)>{{ $t }}</option>@endforeach</select></div>
  </div>
  <div class="row3">
    <div class="fg"><label>Дней</label><input class="in" type="number" min="1" name="duration" value="{{ old('duration',$tour->duration) }}" required></div>
    <div class="fg"><label>Цена, €</label><input class="in" type="number" min="0" step="1" name="price" value="{{ old('price',$tour->price) }}" required></div>
    <div class="fg"><label>Статус</label><select class="in" name="publication_status">@foreach(['draft'=>'Черновик','published'=>'Опубликован','archived'=>'Архив'] as $k=>$l)<option value="{{ $k }}" @selected(old('publication_status',$tour->publication_status)===$k)>{{ $l }}</option>@endforeach</select></div>
  </div>
  <div class="row2"><div class="fg"><label>Обложка (ключ фото)</label><input class="in" name="cover" value="{{ old('cover',$tour->cover) }}" placeholder="places/amalfi"></div><div class="fg"><label>…или загрузить фото</label><input class="in" type="file" name="photo" accept="image/*"></div></div>
  <div><button class="btn btn--solid" type="submit">Сохранить</button></div>
</form>

@if($edit)
<hr class="rule" style="margin:50px 0 30px">
<h2 class="h3" style="margin-bottom:16px">Расписание</h2>
<div class="tbl-wrap"><table class="tbl"><thead><tr><th>Начало</th><th>Вместимость</th><th>Занято</th><th>Статус</th><th></th></tr></thead><tbody>
@foreach($tour->schedules->sortBy('starts_at') as $s)
<tr><form method="post" action="{{ route('admin.schedules.update',$s) }}">@csrf @method('PUT')
<td>{{ ru_date($s->starts_at,'j F Y, H:i') }}</td><td><input class="in" style="width:80px" type="number" name="capacity" min="1" value="{{ $s->capacity }}"></td><td>{{ $s->bookedSeats() }}</td>
<td><select class="in" name="status" style="width:140px">@foreach(['open'=>'Открыт','closed'=>'Закрыт','cancelled'=>'Отменён'] as $k=>$l)<option value="{{ $k }}" @selected($s->status===$k)>{{ $l }}</option>@endforeach</select></td>
<td style="white-space:nowrap"><button class="btn btn--sm" type="submit">Сохранить</button></form>
<form method="post" action="{{ route('admin.schedules.destroy',$s) }}" style="display:inline" onsubmit="return confirm('Удалить дату?')">@csrf @method('DELETE')<button class="link" style="background:none;border-width:0 0 1px;cursor:pointer;margin-left:10px">Удалить</button></form></td></tr>
@endforeach</tbody></table></div>
<form class="form row3" style="margin-top:24px;align-items:end" method="post" action="{{ route('admin.schedules.store',$tour) }}">@csrf
  <div class="fg"><label>Новая дата и время</label><input class="in" type="datetime-local" name="starts_at" required></div>
  <div class="fg"><label>Вместимость</label><input class="in" type="number" min="1" name="capacity" value="10" required></div>
  <div><button class="btn btn--solid btn--sm">Добавить дату</button></div>
</form>
@endif
@endsection
