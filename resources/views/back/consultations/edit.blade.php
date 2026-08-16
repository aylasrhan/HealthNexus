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

    @if((int) $visit->status === 1)
        <section class="hn-panel"><div class="hn-panel-body"><div class="alert alert-warning mb-3">هذه المعاينة مكتملة ومقفلة. لإجراء تعديل يجب تسجيل سبب إعادة فتحها.</div><form method="POST" action="{{ route('consultations.reopen', $visit) }}" class="d-flex align-items-end gap-2">@csrf<div class="form-group flex-grow-1 mb-0"><label for="reopen_reason">سبب إعادة الفتح</label><input id="reopen_reason" name="reason" class="form-control" required minlength="5" maxlength="2000"></div><button class="hn-btn hn-btn-light" type="submit" onclick="return confirm('سيتم تسجيل عملية إعادة الفتح، هل تريد المتابعة؟')"><i class="fe fe-unlock"></i> إعادة فتح المعاينة</button></form></div></section>
    @endif

    <form method="POST" action="{{ route('consultations.update', $visit) }}" class="hn-consultation-form">
        @csrf
        @method('PUT')
        <fieldset @disabled((int) $visit->status === 1)>

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
            <div class="hn-panel-header"><div><h2 class="hn-panel-title"><span class="hn-step">3</span> العلامات الحيوية</h2><p class="hn-panel-subtitle">آخر قياسات مسجلة أثناء هذه الزيارة.</p></div></div>
            <div class="hn-panel-body hn-form-grid">
                @foreach(['temperature'=>'الحرارة °C','systolic_pressure'=>'الضغط الانقباضي','diastolic_pressure'=>'الضغط الانبساطي','pulse'=>'النبض/دقيقة','respiratory_rate'=>'التنفس/دقيقة','oxygen_saturation'=>'الأكسجة %','weight'=>'الوزن كغ','height'=>'الطول سم','blood_sugar'=>'سكر الدم mg/dL'] as $field => $label)
                    <div class="form-group"><label for="vital_{{ $field }}">{{ $label }}</label><input id="vital_{{ $field }}" type="number" step="0.1" name="vitals[{{ $field }}]" class="form-control" value="{{ old('vitals.'.$field, optional($vitals)->{$field}) }}"></div>
                @endforeach
                <div class="form-group"><label>مؤشر كتلة الجسم BMI</label><input class="form-control" value="{{ optional($vitals)->bmi ?: 'يُحسب تلقائيًا' }}" disabled></div>
            </div>
        </section>

        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title"><span class="hn-step">4</span> الفحص والتشخيص</h2><p class="hn-panel-subtitle">نتائج الفحص السريري والتشخيصات المعتمدة.</p></div></div>
            <div class="hn-panel-body">
                <div class="form-group"><label for="clinical_exam">الفحص السريري</label><textarea id="clinical_exam" name="clinical_exam" class="form-control" rows="5" maxlength="8000" placeholder="نتائج المعاينة والفحص السريري...">{{ old('clinical_exam', $clinicalExam) }}</textarea></div>
                <div class="form-group mb-0"><label for="diagnoses">التشخيص وفق ICD-10</label><select id="diagnoses" name="diagnoses[]" class="form-control" multiple data-placeholder="ابحث باسم المرض أو الرمز">@foreach($selectedDiagnosisRows as $diagnosis)<option value="{{ $diagnosis->id }}" selected>{{ $diagnosis->code }} — {{ $diagnosis->name_ar }}</option>@endforeach</select><small class="form-text text-muted">يمكن البحث باسم المرض أو رمز ICD-10 واختيار أكثر من تشخيص.</small></div>
            </div>
        </section>

        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title"><span class="hn-step">5</span> الخطة والخدمات</h2><p class="hn-panel-subtitle">الخدمات المقدمة وأي تعليمات أو ملاحظات لاحقة.</p></div></div>
            <div class="hn-panel-body hn-form-grid">
                <div class="form-group"><label for="services">الخدمات المقدمة</label><select id="services" name="services[]" class="form-control select2" multiple data-placeholder="اختر الخدمات">@foreach($services as $service)<option value="{{ $service->id }}" @selected(in_array($service->id, old('services', $selectedServices)))>{{ $service->name_ar }} — {{ number_format((float)$service->price, 2) }}</option>@endforeach</select><small class="form-text text-muted">تُنشأ الفاتورة تلقائيًا عند إنهاء المعاينة.</small></div>
                <div class="form-group"><label for="doctor_notes">ملاحظات الطبيب وخطة المتابعة</label><textarea id="doctor_notes" name="doctor_notes" class="form-control" rows="5" maxlength="8000" placeholder="التوصيات، المتابعة المطلوبة، أو أي ملاحظات إضافية...">{{ old('doctor_notes', $doctorNotes) }}</textarea></div>
            </div>
        </section>

        <section class="hn-panel">
            <div class="hn-panel-header"><div><h2 class="hn-panel-title"><span class="hn-step">6</span> الوصفة الطبية</h2><p class="hn-panel-subtitle">أضف كل دواء كسطر مستقل مع الجرعة والتكرار والمدة.</p></div><button type="button" id="add-prescription-item" class="hn-btn hn-btn-light"><i class="fe fe-plus"></i> إضافة دواء</button></div>
            <div class="hn-panel-body"><div id="prescription-items">
                @php($rxRows = old('prescription_items', $prescription?->items?->toArray() ?: [[]]))
                @foreach($rxRows as $index => $item)
                    <div class="rx-item hn-panel mb-3" data-index="{{ $index }}"><div class="hn-panel-body hn-form-grid"><div class="form-group"><label>اسم الدواء</label><input class="form-control" name="prescription_items[{{ $index }}][medication_name]" value="{{ $item['medication_name'] ?? '' }}"></div><div class="form-group"><label>الجرعة</label><input class="form-control" name="prescription_items[{{ $index }}][dosage]" value="{{ $item['dosage'] ?? '' }}" placeholder="مثال: 500 mg"></div><div class="form-group"><label>التكرار</label><input class="form-control" name="prescription_items[{{ $index }}][frequency]" value="{{ $item['frequency'] ?? '' }}" placeholder="مرتين يوميًا"></div><div class="form-group"><label>المدة</label><input class="form-control" name="prescription_items[{{ $index }}][duration]" value="{{ $item['duration'] ?? '' }}" placeholder="7 أيام"></div><div class="form-group"><label>طريقة الاستخدام</label><input class="form-control" name="prescription_items[{{ $index }}][route]" value="{{ $item['route'] ?? '' }}" placeholder="فموي، موضعي..."></div><div class="form-group"><label>تعليمات</label><input class="form-control" name="prescription_items[{{ $index }}][instructions]" value="{{ $item['instructions'] ?? '' }}"></div></div><button type="button" class="remove-rx hn-btn hn-btn-light m-3">حذف الدواء</button></div>
                @endforeach
            </div><div class="form-group"><label for="prescription_notes">ملاحظات الوصفة</label><textarea id="prescription_notes" name="prescription_notes" class="form-control" rows="3">{{ old('prescription_notes', $prescription?->notes) }}</textarea></div></div>
        </section>

        <section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title"><span class="hn-step">7</span> موعد المتابعة</h2><p class="hn-panel-subtitle">اختياري؛ ينشئ موعدًا جديدًا بحالة انتظار التأكيد.</p></div></div><div class="hn-panel-body hn-form-grid"><div class="form-group"><label for="follow_up_date">التاريخ</label><input id="follow_up_date" type="date" min="{{ now()->toDateString() }}" name="follow_up_date" class="form-control" value="{{ old('follow_up_date') }}"></div><div class="form-group"><label for="follow_up_time">الوقت</label><input id="follow_up_time" type="time" name="follow_up_time" class="form-control" value="{{ old('follow_up_time') }}"></div></div></section>

        </fieldset>

        @if((int) $visit->status !== 1)<div class="hn-consultation-actions">
            <button class="hn-btn hn-btn-light" type="submit" name="completion_status" value="draft"><i class="fe fe-save"></i> حفظ كمسودة</button>
            <button class="hn-btn hn-btn-primary" type="submit" name="completion_status" value="complete" onclick="return confirm('هل اكتملت بيانات المعاينة وتريد إنهاءها؟')"><i class="fe fe-check-circle"></i> حفظ وإنهاء المعاينة</button>
        </div>@endif
    </form>
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(function () {
        var rxIndex = {{ count($rxRows) }};
        $('#add-prescription-item').on('click', function () {
            var i = rxIndex++;
            $('#prescription-items').append('<div class="rx-item hn-panel mb-3"><div class="hn-panel-body hn-form-grid"><div class="form-group"><label>اسم الدواء</label><input class="form-control" name="prescription_items['+i+'][medication_name]"></div><div class="form-group"><label>الجرعة</label><input class="form-control" name="prescription_items['+i+'][dosage]"></div><div class="form-group"><label>التكرار</label><input class="form-control" name="prescription_items['+i+'][frequency]"></div><div class="form-group"><label>المدة</label><input class="form-control" name="prescription_items['+i+'][duration]"></div><div class="form-group"><label>طريقة الاستخدام</label><input class="form-control" name="prescription_items['+i+'][route]"></div><div class="form-group"><label>تعليمات</label><input class="form-control" name="prescription_items['+i+'][instructions]"></div></div><button type="button" class="remove-rx hn-btn hn-btn-light m-3">حذف الدواء</button></div>');
        });
        $(document).on('click', '.remove-rx', function () { $(this).closest('.rx-item').remove(); });
        var form = $('.hn-consultation-form');
        var dirty = false;
        var autosaveTimer = null;
        var autosave = function () {
            if (!dirty || !form.length || {{ (int) $visit->status }} === 1) return;
            var data = new FormData(form[0]);
            data.set('completion_status', 'draft');
            fetch(form.attr('action'), { method: 'POST', body: data, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (response) { if (!response.ok) throw response; return response.json(); })
                .then(function () { dirty = false; })
                .catch(function () {});
        };
        form.on('change input', ':input', function () { dirty = true; });
        form.on('submit', function () { dirty = false; });
        form.on('change input', ':input', function () { clearTimeout(autosaveTimer); autosaveTimer = setTimeout(autosave, 5000); });
        setInterval(autosave, 30000);
        window.addEventListener('beforeunload', function (event) { if (dirty) { event.preventDefault(); event.returnValue = ''; } });
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
