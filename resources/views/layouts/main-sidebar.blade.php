<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ url('/' . $page='dashboard') }}"><img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo" alt="logo"></a>
        <a class="desktop-logo logo-dark active" href="{{ url('/' . $page='dashboard') }}"><img src="{{URL::asset('assets/img/brand/logo-white.png')}}" class="main-logo dark-theme" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ url('/' . $page='dashboard') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="logo-icon" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ url('/' . $page='dashboard') }}"><img src="{{URL::asset('assets/img/brand/favicon-white.png')}}" class="logo-icon dark-theme" alt="logo"></a>
    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround" src="{{URL::asset('assets/img/faces/6.jpg')}}"><span class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{\Illuminate\Support\Facades\Auth::user()->name}}</h4>
                    <span class="mb-0 text-muted">{{\Illuminate\Support\Facades\Auth::user()->email}}</span>
                    <span class="mb-0 text-muted">{{ optional(Auth::user()->doctor)->subgrp }}</span>
                </div>
            </div>
        </div>
        <ul class="side-menu">
            <li class="side-item side-item-category">WeCare Home</li>
            <li class="slide">
                <a class="side-menu__item" href="{{ url('/' . $page='dashboard') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">الصفحة الرئيسية</span><span class="badge badge-success side-badge">1</span></a>
            </li>
            
            @can('المستخدمين')
            <li class="side-item side-item-category">إدارة النظام</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg><span class="side-menu__label">المستخدمين</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    @can('قائمة المستخدمين')
                        <li><a class="slide-item" href="{{ url('/' . $page='users') }}">قائمة المستخدمين</a></li>
                    @endcan
                    @can('صلاحيات المستخدمين')
                        <li><a class="slide-item" href="{{ url('/' . $page='roles') }}">صلاحيات المستخدمين</a></li>
                    @endcan
                </ul>
            </li>
            @endcan

            <li class="side-item side-item-category">العيادة</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg><span class="side-menu__label">الاختصاصات</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/' . $page='departments') }}">كافة الأقسام</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg><span class="side-menu__label">المرضى</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/' . $page='patients') }}">كافة المرضى</a></li>
                    <li><a class="slide-item" href="{{ url('/' . $page='patients/create') }}">إضافة مريض</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg><span class="side-menu__label">الحجوزات</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('appointments') }}">كافة الحجوزات</a></li>
                    <li><a class="slide-item" href="{{ url('/' . $page='patients/create') }}">إضافة حجز</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg><span class="side-menu__label">عائدات الزيارات</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/' . $page='visits') }}">الدخل والزيارات التابعة لي</a></li>
                </ul>
            </li>

            <li class="side-item side-item-category">التقارير والإعلانات</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg><span class="side-menu__label">التقارير الخاصة بالمركز</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/' . $page='report') }}">تقارير الأمراض والأماكن</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg><span class="side-menu__label">الإعلانات</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/' . $page='ads') }}">جميع الإعلانات</a></li>
                    <li><a class="slide-item" href="{{ url('/' . $page='ads/1') }}">الإعلانات المعروضة</a></li>
                    <li><a class="slide-item" href="{{ url('/' . $page='ads/create') }}">إضافة إعلان</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg><span class="side-menu__label">أسئلة المرضى</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/' . $page='questions') }}">جميع الأسئلة</a></li>
                    <li><a class="slide-item" href="{{ url('/' . $page='questions/user/'.Auth::id()) }}">أسئلتي</a></li>
                    <li><a class="slide-item" href="{{ url('/' . $page='questions/create') }}">إضافة سؤال</a></li>
                </ul>
            </li>

            <li class="side-item side-item-category">الخدمات الإضافية</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg><span class="side-menu__label">البيانات (Database)</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/' . $page='services') }}">الخدمات</a></li>
                </ul>
            </li>
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
@php
    $sidebarUser = auth()->user();
    $sidebarRole = $sidebarUser->primarySystemRole();
    $isManagement = in_array($sidebarRole, ['super_admin', 'secretary'], true);
    $isDoctor = $sidebarRole === 'doctor';
    $isPatient = $sidebarRole === 'patient';
    $doctorSection = $isDoctor ? (int) $sidebarUser->doctor?->subgrp : 0;
@endphp
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll" aria-label="القائمة الرئيسية">
    <div class="main-sidebar-header active">
        <a class="desktop-logo active hn-brand" href="{{ route('dashboard') }}"><span class="hn-brand-mark"><i class="fe fe-plus"></i></span><strong>Health<span>Nexus</span></strong></a>
        <a class="logo-icon mobile-logo active hn-brand-compact" href="{{ route('dashboard') }}" aria-label="HealthNexus"><span class="hn-brand-mark"><i class="fe fe-plus"></i></span></a>
    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix"><div class="user-pro-body"><div class="hn-avatar mx-auto">{{ mb_substr($sidebarUser->name ?? 'م', 0, 1) }}</div><div class="user-info"><h4 class="font-weight-semibold mt-3 mb-0">{{ $sidebarUser->name }}</h4><span class="mb-0 text-muted">{{ \App\Models\User::roleLabel($sidebarRole) }}</span></div></div></div>
        <ul class="side-menu">
            <li class="side-item side-item-category">نظرة عامة</li>
            <li class="slide"><a class="side-menu__item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="side-menu__icon fe fe-grid"></i><span class="side-menu__label">لوحة التحكم</span></a></li>

            @if($isManagement || $isDoctor)
                <li class="side-item side-item-category">{{ $isDoctor ? 'عملي السريري' : 'إدارة الرعاية' }}</li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}"><i class="side-menu__icon fe fe-users"></i><span class="side-menu__label">{{ $isDoctor ? 'مرضاي' : 'المرضى' }}</span></a></li>
                @if($isManagement)
                    <li class="slide"><a class="side-menu__item {{ request()->routeIs('doctors.*') ? 'active' : '' }}" href="{{ route('doctors.index') }}"><i class="side-menu__icon fe fe-briefcase"></i><span class="side-menu__label">الأطباء</span></a></li>
                    <li class="slide"><a class="side-menu__item {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}"><i class="side-menu__icon fe fe-layers"></i><span class="side-menu__label">العيادات والأقسام</span></a></li>
                @endif
                <li class="slide"><a class="side-menu__item {{ request()->is('appointments*') ? 'active' : '' }}" href="{{ url('appointments') }}"><i class="side-menu__icon fe fe-calendar"></i><span class="side-menu__label">{{ $isDoctor ? 'مواعيدي' : 'المواعيد' }}</span></a></li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('visits.*') ? 'active' : '' }}" href="{{ route('visits.index') }}"><i class="side-menu__icon fe fe-activity"></i><span class="side-menu__label">{{ $isDoctor ? 'زياراتي وإيراداتي' : 'الزيارات' }}</span></a></li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('medical-files.*') || request()->routeIs('services.show') ? 'active' : '' }}" href="{{ route('medical-files.index') }}"><i class="side-menu__icon fe fe-file-text"></i><span class="side-menu__label">الملفات الطبية</span></a></li>
            @endif

            @if($isManagement)
                <li class="side-item side-item-category">التشغيل والتقارير</li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}"><i class="side-menu__icon fe fe-file-text"></i><span class="side-menu__label">الفواتير</span></a></li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('questions.*') ? 'active' : '' }}" href="{{ route('questions.index') }}"><i class="side-menu__icon fe fe-message-circle"></i><span class="side-menu__label">أسئلة المرضى</span></a></li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('report.*') ? 'active' : '' }}" href="{{ route('report.index') }}"><i class="side-menu__icon fe fe-bar-chart-2"></i><span class="side-menu__label">التقارير</span></a></li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('analytics.*') ? 'active' : '' }}" href="{{ route('analytics.diseases') }}"><i class="side-menu__icon fe fe-map"></i><span class="side-menu__label">تحليل الأمراض</span></a></li>
                @if($sidebarRole === 'super_admin')
                    <li class="slide"><a class="side-menu__item {{ request()->routeIs('ads.*') ? 'active' : '' }}" href="{{ route('ads.index') }}"><i class="side-menu__icon fe fe-volume-2"></i><span class="side-menu__label">إدارة الإعلانات</span></a></li>
                    <li class="slide"><a class="side-menu__item {{ request()->routeIs('review.*') ? 'active' : '' }}" href="{{ route('review.index') }}"><i class="side-menu__icon fe fe-star"></i><span class="side-menu__label">تقييمات الأطباء</span></a></li>
                @endif
            @elseif($isDoctor)
                <li class="side-item side-item-category">التواصل الطبي</li>
                @if($doctorSection > 0)
                    <li class="slide"><a class="side-menu__item {{ request()->routeIs('questions.show') ? 'active' : '' }}" href="{{ route('questions.show', $doctorSection) }}"><i class="side-menu__icon fe fe-message-circle"></i><span class="side-menu__label">أسئلة عيادتي</span></a></li>
                    <li class="slide"><a class="side-menu__item {{ request()->routeIs('questions.answer') ? 'active' : '' }}" href="{{ route('questions.answer', $doctorSection) }}"><i class="side-menu__icon fe fe-help-circle"></i><span class="side-menu__label">بانتظار إجابتي</span>@if(($unansweredQuestionsCount ?? 0) > 0)<span class="badge badge-danger mr-auto">{{ $unansweredQuestionsCount }}</span>@endif</a></li>
                @endif
            @endif

            @if($sidebarRole === 'super_admin')
                <li class="side-item side-item-category">إدارة النظام</li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="side-menu__icon fe fe-user-check"></i><span class="side-menu__label">المستخدمون</span></a></li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}"><i class="side-menu__icon fe fe-shield"></i><span class="side-menu__label">الأدوار والصلاحيات</span></a></li>
            @endif

            @if($isPatient)
                <li class="side-item side-item-category">حسابي</li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><i class="side-menu__icon fe fe-user"></i><span class="side-menu__label">ملفي الشخصي</span></a></li>
            @endif
        </ul>
    </div>
</aside>
