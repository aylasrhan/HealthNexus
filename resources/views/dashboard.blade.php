@extends('layouts.master')

@section('title', 'لوحة التحكم')

@section('content')
    @php
        $maxTrend = max(1, $trend->max('value'));
        $statusLabels = [0 => ['قيد الانتظار', 'pending'], 1 => ['مؤكد', 'success'], 2 => ['ملغي', 'danger']];
    @endphp

    <div class="hn-page-heading">
        <div>
            <h1>مرحبًا، {{ $user->name }}</h1>
            <p>إليك ملخص العمل وأهم الأنشطة في المركز الصحي اليوم.</p>
        </div>
        <div class="hn-actions">
            <a href="{{ route('patients.create') }}" class="hn-btn hn-btn-light"><i class="fe fe-user-plus"></i> مريض جديد</a>
            <a href="{{ route('patients.index') }}" class="hn-btn hn-btn-primary"><i class="fe fe-calendar"></i> اختيار مريض للحجز</a>
        </div>
    </div>

    <section class="hn-stats" aria-label="الإحصاءات الرئيسية">
        <article class="hn-stat">
            <div class="hn-stat-head"><div><div class="hn-stat-label">إجمالي المرضى</div><div class="hn-stat-value">{{ number_format($stats['patients']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-users"></i></span></div>
            <div class="hn-stat-note">جميع ملفات المرضى المسجلة</div>
        </article>
        <article class="hn-stat hn-stat-success">
            <div class="hn-stat-head"><div><div class="hn-stat-label">الأطباء النشطون</div><div class="hn-stat-value">{{ number_format($stats['doctors']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-briefcase"></i></span></div>
            <div class="hn-stat-note">الأطباء المتاحون ضمن المركز</div>
        </article>
        <article class="hn-stat hn-stat-warning">
            <div class="hn-stat-head"><div><div class="hn-stat-label">مواعيد اليوم</div><div class="hn-stat-value">{{ number_format($stats['today_appointments']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-calendar"></i></span></div>
            <div class="hn-stat-note">{{ number_format($stats['pending_appointments']) }} موعدًا بانتظار الإجراء</div>
        </article>
        <article class="hn-stat hn-stat-danger">
            <div class="hn-stat-head"><div><div class="hn-stat-label">إجمالي الزيارات</div><div class="hn-stat-value">{{ number_format($stats['visits']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-activity"></i></span></div>
            <div class="hn-stat-note">{{ number_format($stats['completed_appointments']) }} موعدًا مؤكدًا</div>
        </article>
    </section>

    <div class="hn-grid">
        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title">نشاط المواعيد</h2><p class="hn-panel-subtitle">عدد المواعيد خلال آخر 7 أيام</p></div><a href="{{ url('appointments') }}" class="tx-13">عرض الكل</a></div>
            <div class="hn-panel-body">
                <div class="hn-chart" role="img" aria-label="مخطط نشاط المواعيد خلال سبعة أيام">
                    @foreach($trend as $item)
                        <div class="hn-chart-item"><span class="hn-chart-value">{{ $item['value'] }}</span><div class="hn-chart-bar-wrap"><div class="hn-chart-bar" style="height: {{ max(4, ($item['value'] / $maxTrend) * 100) }}%"></div></div><span class="hn-chart-label">{{ $item['label'] }}</span></div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title">إجراءات سريعة</h2><p class="hn-panel-subtitle">اختصارات للمهام المتكررة</p></div></div>
            <div class="hn-panel-body hn-quick-actions">
                <a class="hn-quick-action" href="{{ route('patients.create') }}"><i class="fe fe-user-plus"></i><span><strong>تسجيل مريض</strong><small class="d-block text-muted">إنشاء ملف مريض جديد</small></span></a>
                <a class="hn-quick-action" href="{{ route('patients.index') }}"><i class="fe fe-calendar"></i><span><strong>إنشاء موعد</strong><small class="d-block text-muted">ابدأ باختيار ملف المريض</small></span></a>
                <a class="hn-quick-action" href="{{ route('doctors.index') }}"><i class="fe fe-search"></i><span><strong>البحث عن طبيب</strong><small class="d-block text-muted">استعراض الأطباء والدوام</small></span></a>
                <a class="hn-quick-action" href="{{ route('report.index') }}"><i class="fe fe-bar-chart-2"></i><span><strong>فتح التقارير</strong><small class="d-block text-muted">مراجعة مؤشرات المركز</small></span></a>
            </div>
        </section>
    </div>

    <div class="hn-grid">
        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title">مواعيد اليوم</h2><p class="hn-panel-subtitle">قائمة المواعيد المجدولة لهذا اليوم</p></div><a href="{{ url('appointments') }}" class="tx-13">إدارة المواعيد</a></div>
            <div class="hn-table-responsive">
                @if($todayAppointments->isEmpty())
                    <div class="hn-empty"><i class="fe fe-calendar tx-30 d-block mb-2"></i>لا توجد مواعيد مجدولة اليوم.</div>
                @else
                    <table class="hn-table">
                        <thead><tr><th>المريض</th><th>الطبيب</th><th>الوقت</th><th>الحالة</th></tr></thead>
                        <tbody>
                        @foreach($todayAppointments as $appointment)
                            @php($status = $statusLabels[$appointment->status] ?? ['غير محدد', 'pending'])
                            <tr>
                                <td><div class="hn-person"><span class="hn-avatar">{{ mb_substr(optional($appointment->patient)->name ?? 'م', 0, 1) }}</span><div><strong>{{ optional($appointment->patient)->name ?? 'مريض غير محدد' }}</strong><small>#{{ $appointment->id }}</small></div></div></td>
                                <td>{{ optional($appointment->doctor)->name_ar ?? 'غير محدد' }}</td>
                                <td>{{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('h:i A') : '—' }}</td>
                                <td><span class="hn-badge hn-badge-{{ $status[1] }}">{{ $status[0] }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </section>

        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title">المرضى المضافون حديثًا</h2><p class="hn-panel-subtitle">آخر ملفات تم إنشاؤها</p></div><a href="{{ route('patients.index') }}" class="tx-13">عرض المرضى</a></div>
            <div class="hn-panel-body hn-quick-actions">
                @forelse($recentPatients as $patient)
                    <a class="hn-quick-action" href="{{ route('patients.edit', $patient->id) }}"><span class="hn-avatar">{{ mb_substr($patient->f_name ?: optional($patient->user)->name ?: 'م', 0, 1) }}</span><span><strong>{{ trim($patient->f_name.' '.$patient->l_name) ?: optional($patient->user)->name ?: 'مريض' }}</strong><small class="d-block text-muted">{{ $patient->mobile ?: 'لا يوجد رقم هاتف' }}</small></span></a>
                @empty
                    <div class="hn-empty">لم تتم إضافة مرضى بعد.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
