<header class="sticky main-header side-header nav nav-item" aria-label="الشريط العلوي">
    <div class="container-fluid">
        <div class="main-header-left">
            <div class="responsive-logo">
                <a href="{{ route('dashboard') }}" class="hn-brand"><span class="hn-brand-mark"><i class="fe fe-plus"></i></span><strong>We<span>Care</span></strong></a>
            </div>
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#" aria-label="فتح القائمة"><i class="header-icon fe fe-menu"></i></a>
                <a class="close-toggle" href="#" aria-label="إغلاق القائمة"><i class="header-icons fe fe-x"></i></a>
            </div>
            <div class="mr-3 main-header-center d-none d-lg-block">
                <span class="text-muted tx-13">{{ now()->locale('ar')->translatedFormat('l، j F Y') }}</span>
            </div>
        </div>
        <div class="main-header-right">
            <div class="dropdown main-profile-menu nav nav-item nav-link">
                <a class="profile-user d-flex" href="#" data-toggle="dropdown" aria-label="قائمة الحساب">
                    <span class="hn-avatar">{{ mb_substr(auth()->user()->name ?? 'م', 0, 1) }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-left">
                    <div class="p-3 main-header-profile bg-primary">
                        <h6 class="mb-1 text-white">{{ auth()->user()->name }}</h6>
                        <span class="text-white-50">{{ auth()->user()->email }}</span>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bx bx-user-circle"></i> الملف الشخصي</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item" type="submit"><i class="bx bx-log-out"></i> تسجيل الخروج</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
