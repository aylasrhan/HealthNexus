@extends('layouts.master')
@section('title', 'المستخدمون')
@section('content')
<x-ui.page-header title="المستخدمون" description="إدارة حسابات النظام والأدوار وحالة الوصول."><a href="{{ route('users.create') }}" class="hn-btn hn-btn-primary"><i class="fe fe-user-plus"></i> إضافة مستخدم</a></x-ui.page-header>
<x-ui.flash />
<section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title">حسابات النظام</h2><p class="hn-panel-subtitle">{{ number_format($data->total()) }} مستخدمًا</p></div></div>
@if($data->isEmpty())<x-ui.empty title="لا يوجد مستخدمون" icon="fe-users" />@else<div class="hn-table-responsive"><table class="hn-table"><thead><tr><th>المستخدم</th><th>الحالة</th><th>الدور</th><th>تاريخ الإنشاء</th><th>الإجراءات</th></tr></thead><tbody>
@foreach($data as $user)<tr><td><div class="hn-person"><span class="hn-avatar">{{ mb_substr($user->name ?: 'م',0,1) }}</span><div><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></div></div></td><td><span class="hn-badge {{ $user->Status === 'مفعل' ? 'hn-badge-success' : 'hn-badge-danger' }}">{{ $user->Status ?: 'غير محدد' }}</span></td><td>@forelse($user->getRoleNames() as $role)<span class="hn-badge hn-badge-pending">{{ $role }}</span>@empty—@endforelse</td><td>{{ optional($user->created_at)->format('Y/m/d') ?: '—' }}</td><td><div class="hn-row-actions"><a href="{{ route('users.edit',$user->id) }}" class="hn-icon-btn" title="تعديل المستخدم" aria-label="تعديل المستخدم"><i class="fe fe-edit-2"></i></a><form action="{{ route('users.destroy',$user->id) }}" method="POST" onsubmit="return confirm('هل تريد حذف المستخدم؟')">@csrf @method('DELETE')<input type="hidden" name="user_id" value="{{ $user->id }}"><button class="hn-icon-btn hn-icon-btn-danger" title="حذف المستخدم" aria-label="حذف المستخدم"><i class="fe fe-trash-2"></i></button></form></div></td></tr>@endforeach
</tbody></table></div><div class="hn-pagination">{{ $data->withQueryString()->links() }}</div>@endif</section>
@endsection
