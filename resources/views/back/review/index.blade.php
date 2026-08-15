@extends('layouts.master')
@section('title', 'تقييمات الأطباء')
@section('content')
<x-ui.page-header title="تقييمات الأطباء" description="متابعة مؤشرات تقييم الأطباء المسجلة من التطبيق والإدارة." />

@unless($ratingsAvailable)
    <div class="alert alert-info mb-4" role="alert">
        <i class="fe fe-info ml-1"></i>
        إدارة التقييمات متاحة، لكن قاعدة البيانات الحالية لا تحتوي على حقلي عدد التقييمات ومجموعها. تعرض الصفحة الأطباء الآن، وستظهر المؤشرات تلقائيًا عند توفير مصدر بيانات التقييمات.
    </div>
@endunless

<div class="hn-stats">
    <article class="hn-stat"><div class="hn-stat-head"><div><div class="hn-stat-label">إجمالي الأطباء</div><div class="hn-stat-value">{{ number_format($summary['doctors']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-users"></i></span></div></article>
    <article class="hn-stat hn-stat-warning"><div class="hn-stat-head"><div><div class="hn-stat-label">إجمالي التقييمات</div><div class="hn-stat-value">{{ $ratingsAvailable ? number_format($summary['reviews']) : '—' }}</div></div><span class="hn-stat-icon"><i class="fe fe-star"></i></span></div></article>
    <article class="hn-stat hn-stat-success"><div class="hn-stat-head"><div><div class="hn-stat-label">أطباء لديهم تقييم</div><div class="hn-stat-value">{{ $ratingsAvailable ? number_format($summary['rated_doctors']) : '—' }}</div></div><span class="hn-stat-icon"><i class="fe fe-check-circle"></i></span></div></article>
</div>

<section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title">سجل التقييمات المجمع</h2><p class="hn-panel-subtitle">يعرض النظام حاليًا التقييم المجمع لكل طبيب.</p></div></div><div class="hn-panel-body p-0">
@if($doctors->isEmpty())<x-ui.empty title="لا توجد بيانات تقييم" icon="fe-star" />@else
<div class="hn-table-responsive"><table class="hn-table"><thead><tr><th>الطبيب</th><th>العيادة</th><th>عدد التقييمات</th><th>متوسط التقييم</th></tr></thead><tbody>
@foreach($doctors as $doctor)
@php($count = $ratingsAvailable ? (int) ($doctor->revisions_num ?? 0) : null)
<tr><td><div class="hn-person"><span class="hn-avatar">{{ mb_substr($doctor->name_ar ?: 'ط', 0, 1) }}</span><div><strong>{{ $doctor->name_ar ?: ($doctor->user?->name ?: 'طبيب') }}</strong><small>{{ $doctor->specialization_ar ?: 'غير محدد' }}</small></div></div></td><td>{{ $doctor->gnr_m_clinics?->name_ar ?: 'غير محددة' }}</td><td>{{ $ratingsAvailable ? number_format($count) : '—' }}</td><td>@if($ratingsAvailable && $count > 0)<span class="hn-badge hn-badge-pending"><i class="fe fe-star"></i> {{ number_format(((float) $doctor->total_rate) / $count, 1) }} / 5</span>@else — @endif</td></tr>
@endforeach
</tbody></table></div>@endif
</div>@if($doctors->hasPages())<div class="p-3">{{ $doctors->links() }}</div>@endif</section>
@endsection
