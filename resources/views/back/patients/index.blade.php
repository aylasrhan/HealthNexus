@extends('layouts.master')

@section('title', 'المرضى')

@section('content')
    <x-ui.page-header title="المرضى" description="إدارة ملفات المرضى والوصول إلى مواعيدهم وزياراتهم الطبية.">
        @can('create', \App\Models\back\gnr_m_patients::class)
        <a href="{{ route('patients.create') }}" class="hn-btn hn-btn-primary"><i class="fe fe-user-plus"></i> إضافة مريض</a>
        @endcan
    </x-ui.page-header>

    <x-ui.flash />

    <section class="hn-panel">
        <div class="hn-panel-header">
            <div><h2 class="hn-panel-title">قائمة المرضى</h2><p class="hn-panel-subtitle">{{ number_format($patien->total()) }} ملفًا مسجلًا</p></div>
        </div>
        <div class="hn-panel-body border-bottom">
            <form action="{{ route('patients.index') }}" method="GET" class="hn-filter-grid">
                <div class="form-group mb-0"><label for="patient-first-name">الاسم الأول</label><input id="patient-first-name" class="form-control" name="f_name" value="{{ request('f_name') }}" placeholder="ابحث بالاسم"></div>
                <div class="form-group mb-0"><label for="patient-last-name">اسم العائلة</label><input id="patient-last-name" class="form-control" name="l_name" value="{{ request('l_name') }}" placeholder="ابحث باسم العائلة"></div>
                <div class="form-group mb-0"><label for="patient-mobile">رقم الجوال</label><input id="patient-mobile" class="form-control" name="mobile" value="{{ request('mobile') }}" inputmode="tel" placeholder="رقم الجوال"></div>
                <div class="hn-filter-actions"><button class="hn-btn hn-btn-primary" type="submit"><i class="fe fe-search"></i> بحث</button>@if(request()->hasAny(['f_name','l_name','mobile']))<a class="hn-btn hn-btn-light" href="{{ route('patients.index') }}">مسح</a>@endif</div>
            </form>
        </div>

        @if($patien->isEmpty())
            <x-ui.empty title="لا توجد نتائج" description="غيّر كلمات البحث أو أضف مريضًا جديدًا." icon="fe-users" />
        @else
            <div class="hn-table-responsive">
                <table class="hn-table">
                    <thead><tr><th>المريض</th><th>التواصل</th><th>الجنس</th><th>فصيلة الدم</th><th>الموقع</th><th>الإجراءات</th></tr></thead>
                    <tbody id="patients-list">
                    @foreach($patien as $patient)
                        <tr data-patient-row="{{ $patient->id }}">
                            <td><div class="hn-person"><span class="hn-avatar">{{ mb_substr($patient->f_name ?: 'م', 0, 1) }}</span><div><strong>{{ trim($patient->f_name.' '.$patient->l_name) ?: optional($patient->user)->name }}</strong><small>{{ optional($patient->user)->email ?: 'لا يوجد بريد إلكتروني' }}</small></div></div></td>
                            <td><strong class="d-block tx-13">{{ $patient->mobile ?: '—' }}</strong><small class="text-muted">{{ $patient->phone ?: 'لا يوجد هاتف بديل' }}</small></td>
                            <td>{{ (string)$patient->sex === '1' ? 'ذكر' : ((string)$patient->sex === '2' ? 'أنثى' : '—') }}</td>
                            <td><span class="hn-badge hn-badge-pending">{{ $patient->blood ?: 'غير محددة' }}</span></td>
                            <td>{{ optional($patient->gnr_m_cities)->name ?? '—' }}<small class="d-block text-muted">{{ optional($patient->gnr_m_areas)->name ?? '' }}</small></td>
                            <td>
                                <div class="hn-row-actions">
                                    @can('update', $patient)<a href="{{ route('patients.edit', $patient->id) }}" class="hn-icon-btn" title="تعديل بيانات المريض" aria-label="تعديل بيانات المريض"><i class="fe fe-edit-2"></i></a>@endcan
                                    <a href="{{ url('patient-appointments/'.optional($patient->user)->id) }}" class="hn-icon-btn" title="مواعيد المريض" aria-label="مواعيد المريض"><i class="fe fe-calendar"></i></a>
                                    <a href="{{ route('visits.show', $patient->id) }}" class="hn-icon-btn" title="فتح الملف الطبي والزيارات" aria-label="فتح الملف الطبي"><i class="fe fe-file-text"></i></a>
                                    @can('delete', $patient)<form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="js-patient-delete d-inline">@csrf @method('DELETE')<input type="hidden" name="input" value="{{ $patient->id }}"><button class="hn-icon-btn hn-icon-btn-danger" type="submit" title="حذف المريض" aria-label="حذف المريض"><i class="fe fe-trash-2"></i></button></form>@endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="hn-pagination">{{ $patien->withQueryString()->links() }}</div>
        @endif
    </section>
@endsection

@section('js')
<script>
document.querySelectorAll('.js-patient-delete').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!window.confirm('هل أنت متأكد من حذف ملف المريض؟')) return;
        var button = form.querySelector('button');
        button.disabled = true;
        fetch(form.action, {method: 'POST', body: new FormData(form), headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}})
            .then(function (response) { if (!response.ok) throw new Error(); return response.json(); })
            .then(function () { form.closest('tr').remove(); })
            .catch(function () { window.alert('تعذر حذف المريض. يرجى المحاولة مرة أخرى.'); button.disabled = false; });
    });
});
</script>
@endsection
