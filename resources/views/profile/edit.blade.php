@extends('layouts.master')

@section('title', 'الملف الشخصي')
@section('page-header', true)

@section('content')
@php
    $roles = $user->systemRoles();
    $roleLabels = [
        'super_admin' => 'مدير النظام', 'admin' => 'مدير', 'doctor' => 'طبيب',
        'patient' => 'مريض', 'secretary' => 'سكرتارية', 'reception' => 'استقبال',
        'receptionist' => 'استقبال',
    ];
    $roleLabel = collect($roles)->map(fn ($role) => $roleLabels[$role] ?? $role)->join('، ') ?: 'مستخدم';
@endphp

<x-ui.page-header title="الملف الشخصي" description="إدارة بيانات حسابك وكلمة المرور من مكان واحد." />

@if(session('status') === 'profile-updated')
    <div class="alert alert-success"><i class="fe fe-check-circle ml-2"></i> تم تحديث بيانات الملف الشخصي.</div>
@elseif(session('status') === 'password-updated')
    <div class="alert alert-success"><i class="fe fe-shield ml-2"></i> تم تحديث كلمة المرور بنجاح.</div>
@endif

<div class="hn-profile-layout">
    <aside class="hn-profile-summary">
        <div class="hn-profile-cover"></div>
        <div class="hn-profile-summary-body">
            <div class="hn-profile-avatar">{{ mb_strtoupper(mb_substr($user->name ?: 'م', 0, 1)) }}</div>
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->email }}</p>
            <span class="hn-badge hn-badge-success"><i class="fe fe-shield"></i> {{ $roleLabel }}</span>

            <div class="hn-profile-meta">
                <div><span>حالة البريد</span><strong>{{ $user->email_verified_at ? 'موثّق' : 'غير موثّق' }}</strong></div>
                <div><span>رقم الحساب</span><strong>#{{ $user->id }}</strong></div>
            </div>
            <div class="hn-profile-note"><i class="fe fe-info"></i><span>إنشاء الحسابات وتحديد الصلاحيات يتم من خلال المدير فقط.</span></div>
        </div>
    </aside>

    <div class="hn-profile-content">
        <section class="hn-panel">
            <div class="hn-panel-header">
                <div><h2 class="hn-panel-title">البيانات الأساسية</h2><p class="hn-panel-subtitle">حدّث الاسم والبريد الإلكتروني المرتبطين بالحساب.</p></div>
                <span class="hn-section-icon"><i class="fe fe-user"></i></span>
            </div>
            <div class="hn-panel-body">
                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">@csrf</form>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PATCH')
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="name">الاسم الكامل</label>
                            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="email">البريد الإلكتروني</label>
                            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <div class="hn-verification-warning">
                            <span><i class="fe fe-alert-circle"></i> البريد الإلكتروني غير موثّق.</span>
                            <button type="submit" form="send-verification" class="btn btn-link p-0">إعادة إرسال رابط التوثيق</button>
                        </div>
                    @endif
                    <div class="hn-form-footer"><button class="hn-btn hn-btn-primary" type="submit"><i class="fe fe-save"></i> حفظ التغييرات</button></div>
                </form>
            </div>
        </section>

        <section class="hn-panel">
            <div class="hn-panel-header">
                <div><h2 class="hn-panel-title">الأمان وكلمة المرور</h2><p class="hn-panel-subtitle">استخدم كلمة مرور قوية لا تقل عن ثمانية أحرف.</p></div>
                <span class="hn-section-icon"><i class="fe fe-lock"></i></span>
            </div>
            <div class="hn-panel-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label for="current_password">كلمة المرور الحالية</label>
                        <input id="current_password" type="password" name="current_password" class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" autocomplete="current-password">
                        @foreach($errors->updatePassword->get('current_password') as $message)<div class="invalid-feedback d-block">{{ $message }}</div>@endforeach
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="password">كلمة المرور الجديدة</label>
                            <input id="password" type="password" name="password" class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" autocomplete="new-password">
                            @foreach($errors->updatePassword->get('password') as $message)<div class="invalid-feedback d-block">{{ $message }}</div>@endforeach
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="password_confirmation">تأكيد كلمة المرور</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="hn-form-footer"><button class="hn-btn hn-btn-primary" type="submit"><i class="fe fe-shield"></i> تحديث كلمة المرور</button></div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection
