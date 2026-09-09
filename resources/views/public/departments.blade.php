@extends('layouts.public')

@section('content')
<section class="hn-public-page-hero"><span>أقسام WeCare</span><h1>تخصصات متعددة لرعاية متكاملة</h1><p>تعرّف إلى الأقسام والعيادات الفعالة في المركز واختر التخصص الأقرب إلى احتياجك الصحي.</p></section>
<section class="hn-public-section"><div class="hn-public-grid hn-departments-grid">@forelse($clinics as $clinic)<article class="hn-public-card hn-department-public-card"><i class="fe fe-plus-circle"></i><div><h3>{{ $clinic->name_ar }}</h3><p>{{ $clinic->doctor_count }} طبيب مختص ضمن القسم</p></div><span><i class="fe fe-check"></i></span></article>@empty<p class="hn-public-empty">لا توجد أقسام فعالة للعرض حالياً.</p>@endforelse</div></section>
@endsection
