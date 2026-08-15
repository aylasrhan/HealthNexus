@extends('layouts.master')
@section('title', 'لوحة الطبيب')
@section('content')
@php($maxTrend = max(1, $trend->max('value')))
<div class="hn-page-heading"><div><span class="hn-badge hn-badge-success">مساحة الطبيب</span><h1 class="mt-2">مرحبًا د. {{ $doctor->name_ar ?? $user->name }}</h1><p>{{ optional($doctor?->gnr_m_clinics)->name_ar ?? 'لم يتم ربط الحساب بعيادة' }} · ملخص عملك السريري اليوم.</p></div><div class="hn-actions"><a href="{{ url('appointments') }}" class="hn-btn hn-btn-primary"><i class="fe fe-calendar"></i> مواعيدي</a><a href="{{ route('questions.index') }}" class="hn-btn hn-btn-light"><i class="fe fe-message-circle"></i> أسئلة المرضى</a></div></div>

@if(!$doctor)<div class="alert alert-warning">حسابك يحمل دور طبيب لكنه غير مرتبط بسجل في جدول الأطباء. يرجى مراجعة مدير النظام.</div>@endif

<section class="hn-stats">
<article class="hn-stat hn-stat-warning"><div class="hn-stat-head"><div><span class="hn-stat-label">مواعيد اليوم</span><div class="hn-stat-value">{{ number_format($stats['today_appointments']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-calendar"></i></span></div><div class="hn-stat-note">المواعيد المجدولة لك اليوم</div></article>
<article class="hn-stat"><div class="hn-stat-head"><div><span class="hn-stat-label">مرضاي</span><div class="hn-stat-value">{{ number_format($stats['patients']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-users"></i></span></div><div class="hn-stat-note">مرضى لديهم موعد مرتبط بك</div></article>
<article class="hn-stat hn-stat-success"><div class="hn-stat-head"><div><span class="hn-stat-label">زياراتي الطبية</span><div class="hn-stat-value">{{ number_format($stats['visits']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-activity"></i></span></div><div class="hn-stat-note">زيارات كتبت ضمن ملفاتها الطبية</div></article>
<article class="hn-stat hn-stat-danger"><div class="hn-stat-head"><div><span class="hn-stat-label">أسئلة تنتظر الإجابة</span><div class="hn-stat-value">{{ number_format($stats['unanswered_questions']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-message-circle"></i></span></div><div class="hn-stat-note">ضمن عيادتك أو اختصاصك</div></article>
</section>

<div class="hn-grid"><section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title">نشاط مواعيدي</h2><p class="hn-panel-subtitle">آخر سبعة أيام</p></div></div><div class="hn-panel-body"><div class="hn-chart">@foreach($trend as $item)<div class="hn-chart-item"><span class="hn-chart-value">{{ $item['value'] }}</span><div class="hn-chart-bar-wrap"><div class="hn-chart-bar" style="height:{{ max(4,$item['value']*100/$maxTrend) }}%"></div></div><span class="hn-chart-label">{{ $item['label'] }}</span></div>@endforeach</div></div></section>
<section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title">إجراءات الطبيب</h2><p class="hn-panel-subtitle">وصول سريع لمهامك اليومية</p></div></div><div class="hn-panel-body hn-quick-actions"><a class="hn-quick-action" href="{{ url('appointments') }}"><i class="fe fe-calendar"></i><span><strong>إدارة مواعيدي</strong><small class="d-block text-muted">التأكيد والمتابعة اليومية</small></span></a><a class="hn-quick-action" href="{{ route('patients.index') }}"><i class="fe fe-users"></i><span><strong>مرضاي</strong><small class="d-block text-muted">فتح الزيارات والملفات الطبية</small></span></a><a class="hn-quick-action" href="{{ route('questions.index') }}"><i class="fe fe-message-circle"></i><span><strong>الإجابة عن الأسئلة</strong><small class="d-block text-muted">أسئلة القسم الطبي</small></span></a></div></section></div>

<div class="hn-grid"><x-doctor-appointments-panel title="مواعيد اليوم" :appointments="$todayAppointments" empty="لا توجد مواعيد اليوم." /><x-doctor-appointments-panel title="المواعيد القادمة" :appointments="$upcomingAppointments" empty="لا توجد مواعيد قادمة." /></div>
@endsection
