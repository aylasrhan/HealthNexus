@extends('layouts.master')
@section('title', 'الملفات الطبية')
@section('content')
<x-ui.page-header title="الملفات الطبية" description="الوصول إلى ملفات الزيارات والتشخيصات والسوابق والخدمات الطبية." />
<x-ui.flash />

<section class="hn-panel">
    <div class="hn-panel-header">
        <div><h2 class="hn-panel-title">ملفات الزيارات</h2><p class="hn-panel-subtitle">{{ number_format($visits->total()) }} ملفًا</p></div>
        <form method="GET" action="{{ route('medical-files.index') }}" class="d-flex align-items-center">
            <input class="form-control ml-2" type="search" name="search" value="{{ request('search') }}" placeholder="اسم المريض أو رقم الزيارة">
            <button class="hn-btn hn-btn-primary" type="submit"><i class="fe fe-search"></i> بحث</button>
        </form>
    </div>
    <div class="hn-panel-body p-0">
        @if($visits->isEmpty())
            <x-ui.empty title="لا توجد ملفات طبية" description="لا توجد زيارات مطابقة ضمن صلاحياتك." icon="fe-file-text" />
        @else
            <div class="hn-table-responsive"><table class="hn-table">
                <thead><tr><th>رقم الزيارة</th><th>المريض</th><th>العيادة</th><th>التاريخ</th><th>حالة المعاينة</th><th>الإجراء</th></tr></thead>
                <tbody>@foreach($visits as $visit)<tr>
                    <td>#{{ $visit->id }}</td>
                    <td><strong>{{ trim(($visit->f_name ?? '').' '.($visit->l_name ?? '')) ?: 'غير محدد' }}</strong></td>
                    <td>{{ $visit->clinic_name ?: 'غير محددة' }}</td>
                    <td>{{ $visit->d_start ? date('Y/m/d', (int) $visit->d_start) : '—' }}</td>
                    <td><span class="hn-badge {{ (int)$visit->status === 1 ? 'hn-badge-success' : 'hn-badge-pending' }}">{{ (int)$visit->status === 1 ? 'مكتملة' : 'مسودة' }}</span></td>
                    <td><div class="hn-row-actions">
                        <a class="hn-btn hn-btn-light" href="{{ route('services.show', $visit->id) }}"><i class="fe fe-eye"></i> عرض الملف</a>
                        @if(auth()->user()->hasSystemRole('doctor'))<a class="hn-btn hn-btn-primary" href="{{ route('consultations.edit', $visit->id) }}"><i class="fe fe-edit-3"></i> متابعة المعاينة</a>@endif
                    </div></td>
                </tr>@endforeach</tbody>
            </table></div>
        @endif
    </div>
    @if($visits->hasPages())<div class="p-3">{{ $visits->links() }}</div>@endif
</section>
@endsection
