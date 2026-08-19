@extends('layouts.master')
@section('title', 'تقييمات الأطباء')
@section('content')
<x-ui.page-header title="تقييمات الأطباء" description="متابعة مؤشرات تقييم الأطباء المسجلة من التطبيق والإدارة." />

{{-- تم إزالة تنبيه الـ ratingsAvailable لأننا أصبحنا نعتمد على الجدول الجديد --}}

<div class="hn-stats">
    <article class="hn-stat"><div class="hn-stat-head"><div><div class="hn-stat-label">إجمالي الأطباء</div><div class="hn-stat-value">{{ number_format($summary['doctors']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-users"></i></span></div></article>
    <article class="hn-stat hn-stat-warning"><div class="hn-stat-head"><div><div class="hn-stat-label">إجمالي التقييمات</div><div class="hn-stat-value">{{ number_format($summary['reviews']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-star"></i></span></div></article>
    <article class="hn-stat hn-stat-success"><div class="hn-stat-head"><div><div class="hn-stat-label">أطباء لديهم تقييم</div><div class="hn-stat-value">{{ number_format($summary['rated_doctors']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-check-circle"></i></span></div></article>
</div>

<section class="hn-panel">
    <div class="hn-panel-header">
        <div>
            <h2 class="hn-panel-title">سجل التقييمات المجمع</h2>
            <p class="hn-panel-subtitle">يعرض النظام التقييمات الحقيقية المسجلة من المرضى.</p>
        </div>
    </div>
    <div class="hn-panel-body p-0">
        @if($doctors->isEmpty())
            <x-ui.empty title="لا توجد بيانات تقييم" icon="fe-star" />
        @else
            <div class="hn-table-responsive">
                <table class="hn-table">
                    <thead>
                        <tr>
                            <th>الطبيب</th>
                            <th>العيادة</th>
                            <th>عدد التقييمات</th>
                            <th>متوسط التقييم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctors as $doctor)
                            @php
                                $count = $doctor->doctor_reviews_count ?? 0;
                                $avg = $doctor->doctor_reviews_avg_rating ?? 0;
                                
                                // التعديل هنا: سحب الاسم والتخصص والعيادة من جدول الأطباء بدقة
                                $doctorName = $doctor->doctor?->name_ar ?? $doctor->name ?? 'طبيب';
                                $specialization = $doctor->doctor?->specialization_ar ?? 'طبيب عام';
                                $clinicName = $doctor->doctor?->gnr_m_clinics?->name_ar ?? 'غير محددة';
                            @endphp
                            <tr>
                                <td>
                                    <div class="hn-person">
                                        <span class="hn-avatar">{{ mb_substr($doctorName, 0, 1) }}</span>
                                        <div>
                                            <strong>{{ $doctorName }}</strong>
                                            <small>{{ $specialization }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $clinicName }}</td>
                                <td>{{ number_format($count) }}</td>
                                <td>
                                    @if($count > 0)
                                        <span class="hn-badge hn-badge-pending"><i class="fe fe-star"></i> {{ number_format($avg, 1) }} / 5</span>
                                    @else 
                                        — 
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($doctors->hasPages())
        <div class="p-3">{{ $doctors->links() }}</div>
    @endif
</section>
@endsection