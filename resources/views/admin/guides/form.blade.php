@extends('admin.layout')
@section('admin')
@php $edit = $guide->exists; @endphp
<h1 class="h2" style="margin-bottom:26px">{{ $edit ? 'Профиль гида' : 'Новый гид' }}</h1>
<form class="form" method="post" enctype="multipart/form-data" action="{{ $edit ? route('admin.guides.update',$guide) : route('admin.guides.store') }}">@csrf @if($edit)@method('PUT')@endif
  <div class="row2"><div class="fg"><label>Имя</label><input class="in serif" name="name" value="{{ old('name',$guide->name) }}" required></div><div class="fg"><label>Контакты</label><input class="in" name="contact_information" value="{{ old('contact_information',$guide->contact_information) }}"></div></div>
  <div class="row3"><div class="fg"><label>Страна</label><select class="in" name="country">@foreach(config('via.countries') as $c=>$d)<option value="{{ $c }}" @selected(old('country',$guide->country)===$c)>{{ $d['name'] }}</option>@endforeach</select></div><div class="fg"><label>Город</label><input class="in" name="city" value="{{ old('city',$guide->city) }}" required></div><div class="fg"><label>Опыт, лет</label><input class="in" type="number" min="0" name="experience_years" value="{{ old('experience_years',$guide->experience_years) }}" required></div></div>
  <div class="fg"><label>Специализация</label><input class="in" name="specialization" value="{{ old('specialization',$guide->specialization) }}" required></div>
  <div class="fg"><label>Языки (через запятую)</label><input class="in" name="languages" value="{{ old('languages',$guide->languages) }}" required></div>
  <div class="fg"><label>Краткое описание</label><textarea class="in" name="description" required>{{ old('description',$guide->description) }}</textarea></div>
  <div class="fg"><label>Личная история</label><textarea class="in" style="min-height:180px" name="story">{{ old('story',$guide->story) }}</textarea></div>
  <div class="fg"><label>Цитата</label><input class="in" name="quote" value="{{ old('quote',$guide->quote) }}"></div>
  <div class="fg"><label>Портрет</label><input class="in" type="file" name="photo_file" accept="image/*"></div>
  <div><button class="btn btn--solid">Сохранить</button></div>
</form>
@endsection
