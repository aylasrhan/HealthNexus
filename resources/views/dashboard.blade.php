@extends('layouts.master')
@section('css')
    <link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet"/>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">مرحباً بك في لوحة التحكم!</h2>
                <p class="mg-b-0">لوحة التحكم الخاصة بإدارة العيادة والمواعيد (WeCare).</p>
            </div>
        </div>
    </div>
@endsection


@section('title', 'لوحة التحكم')

@section('content')
    <div class="row row-sm">
        <!-- 1. مواعيد اليوم -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">مواعيد اليوم</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <!-- وضع المتغير الحقيقي هنا -->
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $todayAppointments }} موعد</h4>
                                <p class="mb-0 tx-12 text-white op-7">يوجد مواعيد قادمة اليوم</p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-calendar-day text-white tx-30"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
    @php
        $maxTrend = max(1, $trend->max('value'));
        $statusLabels = [0 => ['قيد الانتظار', 'pending'], 1 => ['مؤكد', 'success'], 2 => ['ملغي', 'danger']];
    @endphp

    <div class="hn-page-heading">
        <div>
            <h1>مرحبًا، {{ $user->name }}</h1>
            <p>إليك ملخص العمل وأهم الأنشطة في المركز الصحي اليوم.</p>
        </div>
        
        <!-- 2. طلبات معلقة -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">طلبات معلقة</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <!-- وضع المتغير الحقيقي هنا -->
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $pendingAppointments }} طلبات</h4>
                                <p class="mb-0 tx-12 text-white op-7">بانتظار التأكيد أو الرفض</p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-clock text-white tx-30"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. إجمالي المرضى -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">إجمالي المرضى</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <!-- وضع المتغير الحقيقي هنا -->
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $totalPatients }} مريض</h4>
                                <p class="mb-0 tx-12 text-white op-7">تم تسجيلهم في العيادة</p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-users text-white tx-30"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. الاستشارات المنجزة -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">الاستشارات المنجزة</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <!-- وضع المتغير الحقيقي هنا -->
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $totalVisits }} استشارة</h4>
                                <p class="mb-0 tx-12 text-white op-7">منذ افتتاح العيادة</p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-stethoscope text-white tx-30"></i>
                            </span>
                        </div>
                    </div>
                </div>
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

    <div class="hn-grid hn-dashboard-primary">
        <x-dashboard-calendar :calendar="$calendar" title="تقويم المركز" :url="url('appointments')" />
        <x-dashboard-tasks :tasks="$tasks" title="التنبيهات والمهام" />
    </div>

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
                <a class="hn-quick-action" href="{{ route('medical-files.index') }}"><i class="fe fe-file-text"></i><span><strong>الملفات الطبية</strong><small class="d-block text-muted">البحث في ملفات الزيارات</small></span></a>
                <a class="hn-quick-action" href="{{ route('questions.index') }}"><i class="fe fe-message-circle"></i><span><strong>أسئلة المرضى</strong><small class="d-block text-muted">{{ number_format($operations['unanswered_questions']) }} دون إجابة</small></span></a>
                <a class="hn-quick-action" href="{{ route('analytics.diseases') }}"><i class="fe fe-map"></i><span><strong>تحليل الأمراض</strong><small class="d-block text-muted">الخريطة والمؤشرات الجغرافية</small></span></a>
                @if($user->hasSystemRole('super_admin'))
                    <a class="hn-quick-action" href="{{ route('ads.index') }}"><i class="fe fe-volume-2"></i><span><strong>إدارة الإعلانات</strong><small class="d-block text-muted">{{ number_format($operations['active_ads']) }} إعلان نشط</small></span></a>
                    @if($featureAvailability['doctor_ratings'])<a class="hn-quick-action" href="{{ route('review.index') }}"><i class="fe fe-star"></i><span><strong>تقييمات الأطباء</strong><small class="d-block text-muted">متابعة رضا المرضى</small></span></a>@endif
                @endif
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
