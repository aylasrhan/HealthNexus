<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'WeCare | مركز الرعاية الصحية' }}</title>
    <meta name="description" content="مركز WeCare للرعاية الصحية المتكاملة">
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/wecare.css') }}" rel="stylesheet">
</head>
<body class="hn-landing hn-public">
<header class="hn-landing-nav">
    <a href="{{ route('home') }}" class="hn-brand" aria-label="الصفحة الرئيسية"><span class="hn-brand-mark"><i class="fe fe-plus"></i></span><strong>We<span>Care</span></strong></a>
    <button class="hn-menu-toggle" type="button" aria-label="فتح القائمة" data-public-menu><i class="fe fe-menu"></i></button>
    <nav class="hn-public-nav" data-public-nav>
        <a class="{{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
        <a class="{{ request()->routeIs('center.about') ? 'is-active' : '' }}" href="{{ route('center.about') }}">عن المركز</a>
        <a class="{{ request()->routeIs('center.departments') ? 'is-active' : '' }}" href="{{ route('center.departments') }}">الأقسام</a>
        <a class="{{ request()->routeIs('center.doctors') ? 'is-active' : '' }}" href="{{ route('center.doctors') }}">الأطباء</a>
        <a class="{{ request()->routeIs('center.services') ? 'is-active' : '' }}" href="{{ route('center.services') }}">الخدمات</a>
    </nav>
    <div class="hn-public-actions">
        @auth
            <a href="{{ route('dashboard') }}" class="hn-btn hn-btn-primary">لوحة التحكم</a>
        @else
            <a href="{{ route('login') }}" class="hn-btn hn-btn-light">تسجيل الدخول</a>
        @endauth
    </div>
</header>

<main>@yield('content')</main>

<footer class="hn-public-footer">
    <div><a href="{{ route('home') }}" class="hn-brand"><span class="hn-brand-mark"><i class="fe fe-plus"></i></span><strong>We<span>Care</span></strong></a><p>رعاية صحية منظمة، إنسانية، وقريبة منك.</p></div>
    <div class="hn-footer-links"><a href="{{ route('center.about') }}">عن المركز</a><a href="{{ route('center.departments') }}">الأقسام</a><a href="{{ route('center.doctors') }}">الأطباء</a><a href="{{ route('center.services') }}">الخدمات</a></div>
    <small>© {{ date('Y') }} WeCare — جميع الحقوق محفوظة</small>
</footer>

<script>
    document.querySelector('[data-public-menu]')?.addEventListener('click', () => {
        document.querySelector('[data-public-nav]')?.classList.toggle('is-open');
    });
</script>
</body>
</html>
