@php
    $sidebarUser = auth()->user();
    $sidebarRole = $sidebarUser->primarySystemRole();
    $isManagement = in_array($sidebarRole, ['super_admin', 'secretary'], true);
    $isDoctor = $sidebarRole === 'doctor';
    $isPatient = $sidebarRole === 'patient';
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
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('visits.*') ? 'active' : '' }}" href="{{ route('visits.index') }}"><i class="side-menu__icon fe fe-activity"></i><span class="side-menu__label">{{ $isDoctor ? 'زياراتي الطبية' : 'الزيارات' }}</span></a></li>
            @endif

            @if($isManagement)
                <li class="side-item side-item-category">التشغيل والتقارير</li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('wallet.*') ? 'active' : '' }}" href="{{ route('wallet.index') }}"><i class="side-menu__icon fe fe-credit-card"></i><span class="side-menu__label">المحافظ</span></a></li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('questions.*') ? 'active' : '' }}" href="{{ route('questions.index') }}"><i class="side-menu__icon fe fe-message-circle"></i><span class="side-menu__label">أسئلة المرضى</span></a></li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('report.*') ? 'active' : '' }}" href="{{ route('report.index') }}"><i class="side-menu__icon fe fe-bar-chart-2"></i><span class="side-menu__label">التقارير</span></a></li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('analytics.*') ? 'active' : '' }}" href="{{ route('analytics.diseases') }}"><i class="side-menu__icon fe fe-map"></i><span class="side-menu__label">تحليل الأمراض</span></a></li>
            @elseif($isDoctor)
                <li class="side-item side-item-category">التواصل الطبي</li>
                <li class="slide"><a class="side-menu__item {{ request()->routeIs('questions.*') ? 'active' : '' }}" href="{{ route('questions.index') }}"><i class="side-menu__icon fe fe-message-circle"></i><span class="side-menu__label">أسئلة عيادتي</span></a></li>
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
