@extends('layouts.master')

@section('title', 'الأطباء')

@section('content')
    <x-ui.page-header title="الأطباء" description="استعراض فريق الأطباء وحالة الدوام والتخصصات.">
        <a href="{{ route('doctors.create') }}" class="hn-btn hn-btn-primary"><i class="fe fe-plus"></i> إضافة طبيب</a>
    </x-ui.page-header>

    <x-ui.flash />

    <section class="hn-panel">
        <div class="hn-panel-header">
            <div><h2 class="hn-panel-title">فريق الأطباء</h2><p class="hn-panel-subtitle">{{ number_format(method_exists($doctor, 'total') ? $doctor->total() : $doctor->count()) }} طبيبًا مسجلًا</p></div>
        </div>
        <div class="hn-panel-body">
            @if($doctor->isEmpty())
                <x-ui.empty title="لا يوجد أطباء" description="أضف أول طبيب إلى المركز." icon="fe-briefcase" />
            @else
                <div class="hn-card-grid">
                    @foreach($doctor as $item)
                        <article class="hn-doctor-card">
                            <div class="hn-doctor-top">
                                <img class="hn-doctor-photo" src="{{ $item->photo ? asset('img/'.$item->photo) : asset('assets/img/faces/6.jpg') }}" alt="صورة {{ $item->name_ar }}" loading="lazy">
                                <div class="hn-doctor-meta">
                                    <span class="hn-badge {{ $item->act ? 'hn-badge-success' : 'hn-badge-danger' }}">{{ $item->act ? 'متاح' : 'خارج الدوام' }}</span>
                                    <h3>د. {{ $item->name_ar }}</h3>
                                    <p>{{ $item->specialization_ar ?: 'التخصص غير محدد' }}</p>
                                </div>
                            </div>
                            <div class="hn-doctor-details">
                                <div class="hn-doctor-detail"><small>العيادة</small><strong>{{ optional($item->gnr_m_clinics)->name_ar ?: 'غير محددة' }}</strong></div>
                                <div class="hn-doctor-detail"><small>رقم الهاتف</small><strong>{{ $item->phone_number ?: '—' }}</strong></div>
                                <div class="hn-doctor-detail"><small>بداية الدوام</small><strong>{{ $item->from_time ?: '—' }}</strong></div>
                                <div class="hn-doctor-detail"><small>نهاية الدوام</small><strong>{{ $item->to_time ?: '—' }}</strong></div>
                            </div>
                            <div class="hn-doctor-footer">
                                <span class="text-muted tx-12"><i class="fe fe-star text-warning"></i> {{ number_format((float)($item->total_rate ?? 0), 1) }} · {{ $item->revisions_num ?? 0 }} مراجعة</span>
                                <div class="hn-row-actions">
                                    <a href="{{ route('doctors.edit', ['doctor' => $item->id, 'section' => $item->subgrp]) }}" class="hn-icon-btn" title="تعديل الطبيب" aria-label="تعديل الطبيب"><i class="fe fe-edit-2"></i></a>
                                    <form action="{{ route('doctors.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف الطبيب؟')">@csrf @method('DELETE')<button type="submit" class="hn-icon-btn hn-icon-btn-danger" title="حذف الطبيب" aria-label="حذف الطبيب"><i class="fe fe-trash-2"></i></button></form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                @if(method_exists($doctor, 'links'))<div class="hn-pagination px-0 pb-0 mt-3">{{ $doctor->withQueryString()->links() }}</div>@endif
            @endif
        </div>
    </section>
@endsection
