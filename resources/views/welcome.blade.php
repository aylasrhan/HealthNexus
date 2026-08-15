<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HealthNexus | إدارة الرعاية الصحية</title>
    <meta name="description" content="منصة متكاملة لإدارة المرضى والمواعيد والملفات الطبية">
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/healthnexus.css') }}" rel="stylesheet">
</head>
<body class="hn-landing">
<header class="hn-landing-nav">
    <a href="{{ url('/') }}" class="hn-brand"><span class="hn-brand-mark"><i class="fe fe-plus"></i></span><strong>Health<span>Nexus</span></strong></a>
    <nav>
        @auth
            <a href="{{ route('dashboard') }}" class="hn-btn hn-btn-primary">لوحة التحكم</a>
        @else
            <a href="{{ route('login') }}" class="hn-btn hn-btn-light">تسجيل الدخول</a>
            @if(Route::has('register'))<a href="{{ route('register') }}" class="hn-btn hn-btn-primary">إنشاء حساب</a>@endif
        @endauth
    </nav>
</header>
<main>
    <section class="hn-hero">
        <div class="hn-hero-content">
            <span class="hn-hero-label"><i class="fe fe-heart"></i> منصة الرعاية الصحية المتكاملة</span>
            <h1>إدارة صحية أكثر وضوحًا، من الموعد إلى الملف الطبي</h1>
            <p>HealthNexus يجمع المرضى والأطباء والمواعيد والزيارات والتقارير في مساحة عمل عربية واحدة تساعد فريقك على تقديم رعاية أفضل.</p>
            <div class="hn-actions">
                @auth<a href="{{ route('dashboard') }}" class="hn-btn hn-btn-primary">فتح لوحة التحكم <i class="fe fe-arrow-left"></i></a>
                @else<a href="{{ route('login') }}" class="hn-btn hn-btn-primary">ابدأ الآن <i class="fe fe-arrow-left"></i></a>@endauth
                <a href="#features" class="hn-btn hn-btn-light">اكتشف المزايا</a>
            </div>
        </div>
        <div class="hn-hero-visual"><div class="hn-hero-card"><div class="hn-hero-card-head"><span class="hn-avatar"><i class="fe fe-activity"></i></span><div><strong>نظرة يومية واضحة</strong><small>مؤشرات العمل المهمة</small></div></div><div class="hn-mini-stats"><div><strong>المرضى</strong><span>ملفات منظمة</span></div><div><strong>المواعيد</strong><span>متابعة مباشرة</span></div><div><strong>التقارير</strong><span>قرارات أدق</span></div></div></div></div>
    </section>
    <section id="features" class="hn-landing-features">
        <article><i class="fe fe-users"></i><h2>إدارة المرضى</h2><p>ملفات واضحة تجمع بيانات المريض وزياراته ومواعيده.</p></article>
        <article><i class="fe fe-calendar"></i><h2>تنظيم المواعيد</h2><p>متابعة الحجز والتأكيد والإلغاء من لوحة تشغيل واحدة.</p></article>
        <article><i class="fe fe-file-text"></i><h2>الملفات الطبية</h2><p>وصول أسرع للتاريخ المرضي والتشخيص والخدمات.</p></article>
    </section>
</main>
<footer class="hn-landing-footer">© {{ date('Y') }} HealthNexus — جميع الحقوق محفوظة</footer>
</body>
</html>
