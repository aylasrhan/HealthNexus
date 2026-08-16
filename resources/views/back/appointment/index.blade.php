@extends('layouts.master')

@section('title', 'المواعيد')

@section('content')
    <x-ui.page-header title="المواعيد" description="متابعة الحجوزات وحالات التأكيد والإلغاء.">
        <a href="{{ route('patients.index') }}" class="hn-btn hn-btn-primary"><i class="fe fe-user-plus"></i> اختيار مريض للحجز</a>
    </x-ui.page-header>

    <x-ui.flash />

    <section class="hn-panel">
        <div class="hn-panel-header">
            <div><h2 class="hn-panel-title">قائمة المواعيد</h2><p class="hn-panel-subtitle">{{ number_format(collect($appointments)->count()) }} موعدًا</p></div>
        </div>
        <div class="hn-panel-body border-bottom">
            <form action="{{ url('appointments') }}" method="GET" class="hn-filter-grid hn-filter-grid-wide">
                @if($role !== 'doctor')
                    <div class="form-group mb-0"><label for="appointment-doctor">اسم الطبيب</label><input id="appointment-doctor" class="form-control" name="d_name" value="{{ request('d_name') }}" placeholder="ابحث باسم الطبيب"></div>
                @endif
                <div class="form-group mb-0"><label for="appointment-patient">اسم المريض</label><input id="appointment-patient" class="form-control" name="p_name" value="{{ request('p_name') }}" placeholder="ابحث باسم المريض"></div>
                <div class="form-group mb-0"><label for="appointment-status">الحالة</label><select id="appointment-status" class="form-control" name="status"><option value="">جميع الحالات</option><option value="0" @selected(request('status') === '0')>قيد الانتظار</option><option value="1" @selected(request('status') === '1')>مؤكد</option><option value="2" @selected(request('status') === '2')>ملغي</option></select></div>
                <div class="form-group mb-0"><label for="appointment-date">التاريخ</label><input id="appointment-date" class="form-control" type="date" name="date" value="{{ request('date') }}"></div>
                <div class="form-group mb-0"><label for="appointment-period">الفترة</label><select id="appointment-period" class="form-control" name="period"><option value="">جميع المواعيد</option><option value="today" @selected(request('period') === 'today')>اليوم</option><option value="upcoming" @selected(request('period') === 'upcoming')>القادمة</option></select></div>
                <div class="hn-filter-actions"><button class="hn-btn hn-btn-primary" type="submit"><i class="fe fe-search"></i> تطبيق</button><a class="hn-btn hn-btn-light" href="{{ url('appointments') }}">مسح</a></div>
            </form>
        </div>

        @if(empty($appointments) || collect($appointments)->isEmpty())
            <x-ui.empty title="لا توجد مواعيد" description="لا توجد نتائج مطابقة للفلاتر الحالية." icon="fe-calendar" />
        @else
            <div class="hn-table-responsive">
                <table class="hn-table">
                    <thead><tr><th>المريض</th><th>الطبيب والعيادة</th><th>التاريخ</th><th>الوقت</th><th>الحالة</th><th>الإجراءات</th></tr></thead>
                    <tbody>
                    @foreach($appointments as $appointment)
                        @php
                            $status = match((int)$appointment->status) {1 => ['مؤكد','success'], 2 => ['ملغي','danger'], default => ['قيد الانتظار','pending']};
                            $doctor = $appointment->doctor;
                        @endphp
                        <tr>
                            <td><div class="hn-person"><span class="hn-avatar">{{ mb_substr(optional($appointment->patient)->name ?? 'م', 0, 1) }}</span><div><strong>{{ optional($appointment->patient)->name ?? 'مريض غير محدد' }}</strong><small>#{{ $appointment->id }}</small></div></div></td>
                            <td><strong class="d-block tx-13">{{ optional($doctor)->name_ar ?? 'طبيب غير محدد' }}</strong><small class="text-muted">{{ optional(optional($doctor)->gnr_m_clinics)->name_ar ?? 'العيادة غير محددة' }}</small></td>
                            <td>{{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('Y/m/d') : '—' }}</td>
                            <td>{{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('h:i A') : (optional($appointment->timeSlot)->from ?? '—') }}</td>
                            <td><span class="hn-badge hn-badge-{{ $status[1] }}">{{ $status[0] }}</span></td>
                            <td><div class="hn-row-actions">
                                @if($role === 'doctor' && (int)$appointment->status === 1)
                                    <a class="hn-icon-btn hn-icon-btn-primary" href="{{ route('consultations.start', $appointment) }}" title="بدء أو متابعة المعاينة" aria-label="بدء أو متابعة المعاينة"><i class="fe fe-clipboard"></i></a>
                                @endif
                                @if((int)$appointment->status === 0)<form action="{{ route('appointments.confirm', $appointment) }}" method="POST">@csrf @method('PATCH')<button class="hn-icon-btn" type="submit" title="تأكيد الموعد" aria-label="تأكيد الموعد"><i class="fe fe-check"></i></button></form>@endif
                                @if((int)$appointment->status !== 2)<form action="{{ route('appointments.cancel', $appointment) }}" method="POST" onsubmit="return confirm('هل تريد إلغاء هذا الموعد؟')">@csrf @method('PATCH')<button class="hn-icon-btn hn-icon-btn-danger" type="submit" title="إلغاء الموعد" aria-label="إلغاء الموعد"><i class="fe fe-x"></i></button></form>@endif
                            </div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
