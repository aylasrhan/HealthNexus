@extends('layouts.master')

@section('title', 'زيارات المريض')

@section('content')
    <x-ui.page-header title="زيارات المريض" description="متابعة الزيارات وفتح الملف الطبي المرتبط بكل زيارة.">
        <a href="{{ route('patients.index') }}" class="hn-btn hn-btn-light"><i class="fe fe-arrow-right"></i> العودة للمرضى</a>
        <button class="hn-btn hn-btn-primary" type="button" data-toggle="modal" data-target="#new-visit-modal"><i class="fe fe-plus"></i> إضافة زيارة</button>
    </x-ui.page-header>

    <x-ui.flash />

    <section class="hn-panel">
        <div class="hn-panel-header"><div><h2 class="hn-panel-title">سجل الزيارات</h2><p class="hn-panel-subtitle">{{ number_format($visits->count()) }} زيارة مسجلة</p></div></div>
        @if($visits->isEmpty())
            <x-ui.empty title="لا توجد زيارات" description="يمكنك إنشاء أول زيارة لهذا المريض." icon="fe-activity" />
        @else
            <div class="hn-table-responsive">
                <table class="hn-table">
                    <thead><tr><th>العيادة</th><th>بداية الزيارة</th><th>الحالة</th><th>الملاحظات</th><th>الفاتورة</th><th>الإجراءات</th></tr></thead>
                    <tbody>
                    @foreach($visits as $visit)
                        <tr>
                            <td><strong>{{ optional($visit->gnr_m_clinics)->name_ar ?? 'عيادة غير محددة' }}</strong></td>
                            <td>{{ $visit->d_start ? \Carbon\Carbon::createFromTimestamp((int)$visit->d_start)->format('Y/m/d - h:i A') : '—' }}</td>
                            <td><span class="hn-badge {{ (string)$visit->type === '1' ? 'hn-badge-success' : 'hn-badge-pending' }}">{{ (string)$visit->type === '1' ? 'مكتملة' : 'قيد المتابعة' }}</span></td>
                            <td>{{ \Illuminate\Support\Str::limit($visit->note ?: 'لا توجد ملاحظات', 45) }}</td>
                            <td>@if($visit->invoice)@if(auth()->user()->hasSystemRole('super_admin','secretary'))<a href="{{ route('invoices.show',$visit->invoice) }}"><strong>{{ $visit->invoice->number }}</strong></a>@else<strong>{{ $visit->invoice->number }}</strong>@endif<div class="text-muted">{{ number_format($visit->invoice->total,2) }}</div>@elseif(auth()->user()->hasSystemRole('super_admin','secretary'))<form method="POST" action="{{ route('invoices.store',$visit) }}">@csrf<button class="hn-btn hn-btn-light hn-btn-sm" type="submit">إنشاء فاتورة</button></form>@else—@endif</td>
                            <td><div class="hn-row-actions">
                                @if($visit->cln_m_services->isNotEmpty())
                                    <a href="{{ route('services.show', $visit->id) }}" class="hn-icon-btn" title="عرض الملف الطبي" aria-label="عرض الملف الطبي"><i class="fe fe-file-text"></i></a>
                                @else
                                    <a href="{{ route('MedicalFile.create', ['visit' => $visit->id, 'clinic' => optional($visit->gnr_m_clinics)->id, 'patient' => $visit->patient]) }}" class="hn-icon-btn" title="إنشاء ملف طبي" aria-label="إنشاء ملف طبي"><i class="fe fe-file-plus"></i></a>
                                @endif
                                <a href="{{ route('visits.edit', $visit->id) }}" class="hn-icon-btn" title="تعديل الزيارة" aria-label="تعديل الزيارة"><i class="fe fe-edit-2"></i></a>
                                <form action="{{ route('visits.destroy', $visit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف الزيارة؟')">@csrf @method('DELETE')<input type="hidden" name="input" value="{{ $visit->id }}"><button type="submit" class="hn-icon-btn hn-icon-btn-danger" title="حذف الزيارة" aria-label="حذف الزيارة"><i class="fe fe-trash-2"></i></button></form>
                            </div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <div class="modal fade" id="new-visit-modal" tabindex="-1" role="dialog" aria-labelledby="new-visit-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('visits.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $patient }}">
                    <div class="modal-header"><h5 class="modal-title" id="new-visit-title">إضافة زيارة جديدة</h5><button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body">
                        <div class="form-group"><label for="visit-clinic">العيادة <span class="text-danger">*</span></label><select id="visit-clinic" name="clinic" class="form-control" required><option value="">اختر العيادة</option>@foreach($clinics as $clinic)<option value="{{ $clinic->id }}">{{ $clinic->name_ar }}</option>@endforeach</select></div>
                        <div class="form-group"><label for="visit-note">ملاحظات الزيارة</label><textarea id="visit-note" name="note" class="form-control" rows="3" placeholder="أدخل ملاحظة مختصرة"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="hn-btn hn-btn-light" data-dismiss="modal">إلغاء</button><button type="submit" class="hn-btn hn-btn-primary"><i class="fe fe-check"></i> حفظ الزيارة</button></div>
                </form>
            </div>
        </div>
    </div>
@endsection
