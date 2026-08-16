@extends('layouts.master')
@section('title', 'الإعلانات')
@section('content')
<x-ui.page-header title="الإعلانات" description="إدارة الرسائل والإعلانات المعروضة في تطبيق المرضى."><a href="{{ route('ads.create') }}" class="hn-btn hn-btn-primary"><i class="fe fe-plus"></i> إعلان جديد</a></x-ui.page-header>
<x-ui.flash />
<section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title">الإعلانات المنشورة</h2><p class="hn-panel-subtitle">{{ number_format($ads->count()) }} إعلانًا</p></div></div><div class="hn-panel-body">
@if($ads->isEmpty())<x-ui.empty title="لا توجد إعلانات" description="أنشئ أول إعلان ليظهر للمستخدمين." icon="fe-volume-2" />@else
<div class="hn-card-grid">@foreach($ads as $ad)<article class="hn-ad-card">@if($ad->img)<img src="{{ asset('img/'.$ad->img) }}" alt="صورة الإعلان" loading="lazy">@endif<div class="hn-ad-body"><span class="hn-badge {{ $ad->statue ? 'hn-badge-success' : 'hn-badge-danger' }}">{{ $ad->statue ? 'نشط' : 'غير نشط' }}</span><p>{{ $ad->text }}</p></div><div class="hn-doctor-footer"><small class="text-muted">{{ $ad->time() }}</small><div class="hn-row-actions"><a href="{{ route('ads.edit', $ad->id) }}" class="hn-icon-btn" aria-label="تعديل الإعلان" title="تعديل الإعلان"><i class="fe fe-edit-2"></i></a><form action="{{ route('ads.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('هل تريد حذف الإعلان؟')">@csrf @method('DELETE')<input type="hidden" name="input" value="{{ $ad->id }}"><input type="hidden" name="img" value="{{ $ad->img }}"><button class="hn-icon-btn hn-icon-btn-danger" type="submit" aria-label="حذف الإعلان" title="حذف الإعلان"><i class="fe fe-trash-2"></i></button></form></div></div></article>@endforeach</div>@endif
</div></section>
@endsection
