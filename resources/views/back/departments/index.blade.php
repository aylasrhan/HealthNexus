@extends('layouts.master')
@section('title', 'العيادات والأقسام')
@section('content')
<x-ui.page-header title="العيادات والأقسام" description="إدارة تخصصات المركز واستعراض الأطباء في كل قسم."><a href="{{ route('departments.create') }}" class="hn-btn hn-btn-primary"><i class="fe fe-plus"></i> إضافة قسم</a></x-ui.page-header>
<x-ui.flash />
<section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title">أقسام المركز</h2><p class="hn-panel-subtitle">{{ number_format($departments->count()) }} قسمًا</p></div></div><div class="hn-panel-body">
@if($departments->isEmpty())<x-ui.empty title="لا توجد أقسام" description="أضف أول قسم أو عيادة إلى المركز." icon="fe-layers" />@else
<div class="hn-card-grid">@foreach($departments as $department)<a href="{{ route('doctors.show', $department->id) }}" class="hn-department-card"><span class="hn-stat-icon"><i class="fe fe-layers"></i></span><div><h3>{{ $department->name_ar }}</h3><p>عرض الأطباء والخدمات المرتبطة</p></div><i class="fe fe-chevron-left mr-auto"></i></a>@endforeach</div>@endif
</div></section>
@endsection
