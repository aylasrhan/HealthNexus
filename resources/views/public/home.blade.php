@extends('layouts.public')

@section('content')
<section class="hn-hero hn-public-hero">
    <div class="hn-hero-content">
        <span class="hn-hero-label"><i class="fe fe-heart"></i> مركز رعاية صحية متكامل</span>
        <h1>رعاية موثوقة تبدأ من احتياجك وتنتهي براحتك</h1>
        <p>نجمع الأقسام الطبية والأطباء والخدمات في مركز واحد، لتصل إلى الرعاية المناسبة بخطوات أبسط وتجربة أكثر إنسانية.</p>
        <div class="hn-actions"><a href="{{ route('center.departments') }}" class="hn-btn hn-btn-primary">استكشف الأقسام <i class="fe fe-arrow-left"></i></a><a href="{{ route('center.doctors') }}" class="hn-btn hn-btn-light">تعرّف إلى أطبائنا</a></div>
    </div>
    <div class="hn-hero-visual"><div class="hn-hero-card"><div class="hn-hero-card-head"><span class="hn-avatar"><i class="fe fe-activity"></i></span><div><strong>الرعاية المناسبة في الوقت المناسب</strong><small>من الاستقبال حتى المتابعة الطبية</small></div></div><div class="hn-mini-stats"><div><strong>{{ $statistics['clinics'] }}</strong><span>قسم وعيادة</span></div><div><strong>{{ $statistics['doctors'] }}</strong><span>طبيب مختص</span></div><div><strong>{{ $statistics['services'] }}</strong><span>خدمة طبية</span></div></div></div></div>
</section>

<section class="hn-public-section">
    <div class="hn-section-heading"><div><span>أقسامنا الطبية</span><h2>العناية التي تحتاجها، في مكان واحد</h2></div><a href="{{ route('center.departments') }}">عرض جميع الأقسام <i class="fe fe-arrow-left"></i></a></div>
    <div class="hn-public-grid hn-departments-grid">
        @forelse($clinics as $clinic)
            <a class="hn-public-card hn-department-public-card" href="{{ route('center.departments') }}"><i class="fe fe-plus-circle"></i><div><h3>{{ $clinic->name_ar }}</h3><p>{{ $clinic->doctor_count }} طبيب مختص ضمن القسم</p></div><span><i class="fe fe-arrow-left"></i></span></a>
        @empty
            <p class="hn-public-empty">ستظهر الأقسام الطبية هنا بعد إضافتها إلى النظام.</p>
        @endforelse
    </div>
</section>

<section class="hn-public-section hn-public-muted">
    <div class="hn-section-heading"><div><span>فريقنا الطبي</span><h2>أطباء مختصون لرعايتك</h2></div><a href="{{ route('center.doctors') }}">عرض جميع الأطباء <i class="fe fe-arrow-left"></i></a></div>
    <div class="hn-public-grid hn-doctors-public-grid">
        @forelse($doctors as $doctor)
            @include('public.partials.doctor-card', ['doctor' => $doctor])
        @empty
            <p class="hn-public-empty">ستظهر بيانات الأطباء هنا بعد إضافتهم إلى النظام.</p>
        @endforelse
    </div>
</section>

<section class="hn-public-section">
    <div class="hn-section-heading"><div><span>خدماتنا</span><h2>خدمات طبية مصممة لراحتك</h2></div><a href="{{ route('center.services') }}">عرض جميع الخدمات <i class="fe fe-arrow-left"></i></a></div>
    <div class="hn-public-grid hn-services-public-grid">
        @forelse($services as $service)
            @include('public.partials.service-card', ['service' => $service])
        @empty
            <p class="hn-public-empty">ستظهر الخدمات المتاحة هنا بعد إضافتها إلى النظام.</p>
        @endforelse
    </div>
</section>

<section class="hn-public-cta"><div><span><i class="fe fe-calendar"></i></span><div><h2>هل أنت مستعد لبدء رحلة رعايتك؟</h2><p>سجّل الدخول للوصول إلى المواعيد وملفك الطبي ومتابعة خدماتك.</p></div></div><a href="{{ route('login') }}" class="hn-btn hn-btn-light">تسجيل الدخول <i class="fe fe-arrow-left"></i></a></section>
@endsection
