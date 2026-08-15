@extends('layouts.master')

@section('title', 'المعاينة الطبية')

@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <x-ui.page-header title="المعاينة الطبية" description="تسجيل تفاصيل الجلسة والتشخيص والخدمات ضمن ملف الزيارة.">
        <a href="{{ url('appointments') }}" class="hn-btn hn-btn-light"><i class="fe fe-arrow-right"></i> المواعيد</a>
        <a href="{{ route('services.show', $visit) }}" class="hn-btn hn-btn-light"><i class="fe fe-file-text"></i> الملف التفصيلي</a>
    </x-ui.page-header>

    <x-ui.flash />

    @if($errors->any())
        <div class="alert alert-danger mb-4"><strong>تعذر حفظ المعاينة:</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="hn-consultation-summary">
        <div class="hn-consultation-patient"><span class="hn-avatar">{{ mb_substr($patientProfile->f_name ?: 'م', 0, 1) }}</span><div><small>المريض</small><strong>{{ trim($patientProfile->f_name.' '.$patientProfile->l_name) }}</strong></div></div>
        <div><small>رقم الزيارة</small><strong>#{{ $visit->id }}</strong></div>
        <div><small>العيادة</small><strong>{{ $visit->gnr_m_clinics->name_ar ?: 'غير محددة' }}</strong></div>
        <div><small>تاريخ الزيارة</small><strong>{{ $visit->d_start ? date('Y/m/d h:i A', (int) $visit->d_start) : '—' }}</strong></div>
        <span class="hn-badge {{ (int)$visit->status === 1 ? 'hn-badge-success' : 'hn-badge-pending' }}">{{ (int)$visit->status === 1 ? 'مكتملة' : 'مسودة' }}</span>
    </section>

    <form method="POST" action="{{ route('consultations.update', $visit) }}" class="hn-consultation-form">
        @csrf
        @method('PUT')

        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title"><span class="hn-step">1</span> القصة السريرية</h2><p class="hn-panel-subtitle">دوّن سبب الزيارة وتطور الحالة بدقة.</p></div></div>
            <div class="hn-panel-body hn-form-grid">
                <div class="form-group"><label for="chief_complaint">الشكوى الرئيسية</label><textarea id="chief_complaint" name="chief_complaint" class="form-control" rows="4" maxlength="4000" placeholder="ما السبب الرئيسي لقدوم المريض؟">{{ old('chief_complaint', $chiefComplaint) }}</textarea></div>
                <div class="form-group"><label for="history">القصة المرضية الحالية</label><textarea id="history" name="history" class="form-control" rows="4" maxlength="8000" placeholder="بداية الأعراض، مدتها، تطورها والعوامل المصاحبة...">{{ old('history', $history) }}</textarea></div>
            </div>
        </section>

        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title"><span class="hn-step">2</span> السوابق المرضية</h2><p class="hn-panel-subtitle">كل حالة تختارها تُحفظ كسجل مستقل في ملف المريض، ويمكن تسجيل عدة حالات دفعة واحدة.</p></div></div>
            <div class="hn-panel-body hn-history-grid">
                @foreach($medicalHistoryCategories as $category)
                    @php($selectedRows = $selectedMedicalHistory->get($category->id, collect()))
                    <details class="hn-history-card" @if($selectedRows->isNotEmpty()) open @endif>
                        <summary><span>{{ $category->name_ar }}</span><span class="hn-badge {{ $selectedRows->isNotEmpty() ? 'hn-badge-success' : 'hn-badge-pending' }}">{{ $selectedRows->count() ?: 'لا يوجد' }}</span></summary>
                        <div class="hn-history-card-body">
                        <div class="form-group"><label for="history-category-{{ $category->id }}">{{ $category->name_ar }}</label><select id="history-category-{{ $category->id }}" class="form-control medical-history-select" name="medical_history[{{ $category->id }}][]" multiple data-category="{{ $category->id }}" data-placeholder="ابحث واختر">@foreach($selectedRows as $row)<option value="{{ $row->id }}" selected>{{ $row->name_ar }}</option>@endforeach</select></div>
                        <div class="form-group mb-0"><label for="history-note-{{ $category->id }}">ملاحظة</label><textarea id="history-note-{{ $category->id }}" class="form-control" name="history_notes[{{ $category->id }}]" rows="2" maxlength="2000">{{ old('history_notes.'.$category->id, $medicalHistoryNotes[$category->id] ?? '') }}</textarea></div>
                        </div>
                    </details>
                @endforeach
            </div>
        </section>

        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title"><span class="hn-step">3</span> الفحص والتشخيص</h2><p class="hn-panel-subtitle">نتائج الفحص السريري والتشخيصات المعتمدة.</p></div></div>
            <div class="hn-panel-body">
                <div class="form-group"><label for="clinical_exam">الفحص السريري</label><textarea id="clinical_exam" name="clinical_exam" class="form-control" rows="5" maxlength="8000" placeholder="نتائج المعاينة والفحص السريري...">{{ old('clinical_exam', $clinicalExam) }}</textarea></div>
                <div class="form-group mb-0"><label for="diagnoses">التشخيص وفق ICD-10</label><select id="diagnoses" name="diagnoses[]" class="form-control" multiple data-placeholder="ابحث باسم المرض أو الرمز">@foreach($selectedDiagnosisRows as $diagnosis)<option value="{{ $diagnosis->id }}" selected>{{ $diagnosis->code }} — {{ $diagnosis->name_ar }}</option>@endforeach</select><small class="form-text text-muted">يمكن البحث باسم المرض أو رمز ICD-10 واختيار أكثر من تشخيص.</small></div>
            </div>
        </section>

        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title"><span class="hn-step">4</span> الخطة والخدمات</h2><p class="hn-panel-subtitle">الخدمات المقدمة وأي تعليمات أو ملاحظات لاحقة.</p></div></div>
            <div class="hn-panel-body hn-form-grid">
                <div class="form-group"><label for="services">الخدمات المقدمة</label><select id="services" name="services[]" class="form-control select2" multiple data-placeholder="اختر الخدمات">@foreach($services as $service)<option value="{{ $service->id }}" @selected(in_array($service->id, old('services', $selectedServices)))>{{ $service->name_ar }}</option>@endforeach</select></div>
                <div class="form-group"><label for="doctor_notes">ملاحظات الطبيب وخطة المتابعة</label><textarea id="doctor_notes" name="doctor_notes" class="form-control" rows="5" maxlength="8000" placeholder="التوصيات، المتابعة المطلوبة، أو أي ملاحظات إضافية...">{{ old('doctor_notes', $doctorNotes) }}</textarea></div>
            </div>
        </section>

        <div class="hn-consultation-actions">
            <button class="hn-btn hn-btn-light" type="submit" name="completion_status" value="draft"><i class="fe fe-save"></i> حفظ كمسودة</button>
            <button class="hn-btn hn-btn-primary" type="submit" name="completion_status" value="complete" onclick="return confirm('هل اكتملت بيانات المعاينة وتريد إنهاءها؟')"><i class="fe fe-check-circle"></i> حفظ وإنهاء المعاينة</button>
        </div>
    </form>
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(function () {
        if ($.fn.select2) {
            $('#diagnoses').select2({
                width: '100%',
                dir: 'rtl',
                minimumInputLength: 2,
                placeholder: 'اكتب حرفين على الأقل للبحث',
                language: { inputTooShort: function () { return 'اكتب حرفين على الأقل'; }, searching: function () { return 'جارٍ البحث...'; }, noResults: function () { return 'لا توجد نتائج'; } },
                ajax: {
                    url: @json(route('consultations.diagnoses.search')),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) { return { q: params.term || '' }; },
                    processResults: function (data) { return data; },
                    cache: true
                }
            });
            $('#services').select2({ width: '100%', dir: 'rtl' });
            $('.medical-history-select').each(function () {
                var field = $(this);
                field.select2({
                    width: '100%', dir: 'rtl', minimumInputLength: 2,
                    placeholder: 'اكتب للبحث',
                    ajax: {
                        url: @json(route('consultations.medical-history.search')),
                        dataType: 'json', delay: 250,
                        data: function (params) { return { q: params.term || '', cat: field.data('category') }; },
                        processResults: function (data) { return data; }, cache: true
                    }
                });
            });
        }
    });
</script>
@endsection
