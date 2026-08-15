@extends('layouts.master')
@section('title', 'التقارير')
@section('content')
<x-ui.page-header title="التقارير التشغيلية" description="مؤشرات المرضى والزيارات والمواعيد حسب الفترة والمنطقة والعيادة." />
<x-ui.flash />

<section class="hn-panel mb-4">
    <div class="hn-panel-body">
        <form method="GET" action="{{ route('report.index') }}" class="hn-report-filters">
            <div><label for="report-from">من تاريخ</label><input id="report-from" type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}"></div>
            <div><label for="report-to">إلى تاريخ</label><input id="report-to" type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}"></div>
            <div><label for="report-area">المنطقة</label><select id="report-area" name="area" class="form-control"><option value="">كل المناطق</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected((string)$filters['area'] === (string)$area->id)>{{ $area->name }}</option>@endforeach</select></div>
            <div><label for="report-clinic">العيادة</label><select id="report-clinic" name="clinic" class="form-control"><option value="">كل العيادات</option>@foreach($clinics as $clinic)<option value="{{ $clinic->id }}" @selected((string)$filters['clinic'] === (string)$clinic->id)>{{ $clinic->name_ar ?: $clinic->name_en }}</option>@endforeach</select></div>
            <div class="hn-filter-actions"><button class="hn-btn hn-btn-primary" type="submit"><i class="fe fe-filter"></i> تطبيق</button><a href="{{ route('report.index') }}" class="hn-btn hn-btn-light">مسح</a></div>
        </form>
        <form method="POST" action="{{ route('report.store') }}" class="mt-3">@csrf
            @foreach(['date_from','date_to','area','clinic'] as $filter)<input type="hidden" name="{{ $filter }}" value="{{ $filters[$filter] }}">@endforeach
            <button class="hn-btn hn-btn-light" type="submit"><i class="fe fe-download"></i> تصدير الزيارات CSV</button>
        </form>
    </div>
</section>

<div class="hn-stats">
    <article class="hn-stat"><div class="hn-stat-head"><div><span class="hn-stat-label">المرضى</span><div class="hn-stat-value">{{ number_format($summary['patients']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-users"></i></span></div><div class="hn-stat-note">ضمن المنطقة المحددة</div></article>
    <article class="hn-stat hn-stat-success"><div class="hn-stat-head"><div><span class="hn-stat-label">الزيارات</span><div class="hn-stat-value">{{ number_format($summary['visits']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-activity"></i></span></div><div class="hn-stat-note">ضمن جميع الفلاتر</div></article>
    <article class="hn-stat hn-stat-warning"><div class="hn-stat-head"><div><span class="hn-stat-label">المواعيد</span><div class="hn-stat-value">{{ number_format($summary['appointments']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-calendar"></i></span></div><div class="hn-stat-note">غير المحذوفة</div></article>
    <article class="hn-stat"><div class="hn-stat-head"><div><span class="hn-stat-label">العيادات النشطة</span><div class="hn-stat-value">{{ number_format($summary['clinics']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-grid"></i></span></div><div class="hn-stat-note">عيادات لديها زيارات</div></article>
</div>

<div class="hn-grid">
    <section class="hn-panel">
        <div class="hn-panel-header"><div><h2 class="hn-panel-title">أكثر العيادات استقبالًا للزيارات</h2><p class="hn-panel-subtitle">مرتبة حسب عدد الزيارات المسجلة.</p></div></div>
        <div class="hn-panel-body">
            @forelse($topClinics as $clinic)
                <div class="hn-report-row"><div><strong>{{ $clinic->clinic_name }}</strong><span>{{ number_format($clinic->total) }} زيارة</span></div><div class="hn-report-bar"><span style="width:{{ $topClinics->max('total') ? max(5, round(($clinic->total / $topClinics->max('total')) * 100)) : 0 }}%"></span></div></div>
            @empty<x-ui.empty title="لا توجد بيانات" description="لا توجد زيارات ضمن الفلاتر المختارة." icon="fe-bar-chart-2" />@endforelse
        </div>
    </section>
    <section class="hn-panel">
        <div class="hn-panel-header"><div><h2 class="hn-panel-title">أكثر التشخيصات تسجيلًا</h2><p class="hn-panel-subtitle">أعلى التشخيصات في الفترة المحددة.</p></div></div>
        <div class="hn-panel-body">
            @forelse($topDiagnoses as $diagnosis)<div class="hn-ranked-item"><span>{{ $loop->iteration }}</span><div><strong>{{ $diagnosis->diagnosis }}</strong><small>{{ number_format($diagnosis->total) }} مرة</small></div></div>@empty<x-ui.empty title="لا توجد تشخيصات" description="لم تسجل تشخيصات ضمن النطاق المختار." icon="fe-file-text" />@endforelse
        </div>
    </section>
</div>

<section class="hn-panel">
    <div class="hn-panel-header"><div><h2 class="hn-panel-title">أحدث الزيارات</h2><p class="hn-panel-subtitle">آخر عشر زيارات مطابقة للفلاتر.</p></div></div>
    @if($recentVisits->isEmpty())<x-ui.empty title="لا توجد زيارات" description="جرّب تغيير الفترة أو الفلاتر." icon="fe-activity" />@else
    <div class="hn-table-responsive"><table class="hn-table"><thead><tr><th>#</th><th>المريض</th><th>العيادة</th><th>التاريخ</th><th>الحالة</th></tr></thead><tbody>
    @foreach($recentVisits as $visit)<tr><td>{{ $visit->id }}</td><td>{{ trim($visit->f_name.' '.$visit->l_name) ?: 'غير محدد' }}</td><td>{{ $visit->clinic_name ?: 'غير محددة' }}</td><td>{{ $visit->d_start ? \Carbon\Carbon::createFromTimestamp((int)$visit->d_start)->format('Y-m-d H:i') : '—' }}</td><td><span class="hn-badge {{ (string)$visit->status === '1' ? 'hn-badge-success' : 'hn-badge-pending' }}">{{ (string)$visit->status === '1' ? 'مكتملة' : 'مفتوحة' }}</span></td></tr>@endforeach
    </tbody></table></div>@endif
</section>
@endsection
