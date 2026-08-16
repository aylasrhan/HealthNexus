@extends('layouts.master')
@section('css')
    <link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet"/>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">مرحباً بك في لوحة التحكم!</h2>
                <p class="mg-b-0">لوحة التحكم الخاصة بإدارة العيادة والمواعيد (WeCare).</p>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-sm">
        <!-- 1. مواعيد اليوم -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">مواعيد اليوم</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <!-- وضع المتغير الحقيقي هنا -->
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $todayAppointments }} موعد</h4>
                                <p class="mb-0 tx-12 text-white op-7">يوجد مواعيد قادمة اليوم</p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-calendar-day text-white tx-30"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 2. طلبات معلقة -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">طلبات معلقة</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <!-- وضع المتغير الحقيقي هنا -->
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $pendingAppointments }} طلبات</h4>
                                <p class="mb-0 tx-12 text-white op-7">بانتظار التأكيد أو الرفض</p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-clock text-white tx-30"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. إجمالي المرضى -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">إجمالي المرضى</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <!-- وضع المتغير الحقيقي هنا -->
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $totalPatients }} مريض</h4>
                                <p class="mb-0 tx-12 text-white op-7">تم تسجيلهم في العيادة</p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-users text-white tx-30"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. الاستشارات المنجزة -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">الاستشارات المنجزة</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <!-- وضع المتغير الحقيقي هنا -->
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $totalVisits }} استشارة</h4>
                                <p class="mb-0 tx-12 text-white op-7">منذ افتتاح العيادة</p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-stethoscope text-white tx-30"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول المواعيد -->
    <div class="row row-sm">
        <div class="col-md-12 col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-header pb-1">
                    <h3 class="card-title mb-2">أحدث المواعيد</h3>
                    <p class="tx-12 mb-0 text-muted">قائمة بآخر 5 مواعيد تم حجزها في العيادة.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mg-b-0 text-md-nowrap">
                            <thead>
                                <tr>
                                    <th>اسم المريض</th>
                                    <th>تاريخ الموعد</th>
                                    <th>الوقت</th>
                                    <th>حالة الموعد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- حلقة التكرار لجلب البيانات من الداتابيز -->
                                @foreach($recentAppointments as $app)
                                <tr>
                                <td>{{ $app->patient->name ?? 'مريض غير معروف' }}</td>
                                <!-- <td>مريض رقم {{ $app->appointment_for }}</td> -->
                                    <td>{{ $app->appointment_date }}</td>
                                    <td>{{ $app->time ?? $app->available_time ?? '--:--' }}</td>
                                    <td>
                                        @if($app->status == 0)
                                            <span class="badge badge-warning">قيد الانتظار</span>
                                        @elseif($app->status == 1)
                                            <span class="badge badge-success">مؤكد</span>
                                        @else
                                            <span class="badge badge-danger">مرفوض</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{URL::asset('assets/js/index.js')}}"></script>
@endsection