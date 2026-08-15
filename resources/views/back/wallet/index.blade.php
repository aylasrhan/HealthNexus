@extends('layouts.master')

@section('title', 'المحافظ')

@section('content')
    <x-ui.page-header title="المحافظ" description="متابعة سجل الحركات المالية المسجلة في النظام." />

    <div class="alert alert-info mb-4" role="alert">
        <i class="fe fe-info ml-1"></i>
        قاعدة البيانات الحالية لا تحتوي على حقل يربط حركة المحفظة بمريض محدد؛ لذلك تعرض هذه الصفحة السجل المالي العام فقط، ولا تتيح الإضافة أو السحب من ملف المريض.
    </div>

    <div class="hn-stats">
        <article class="hn-stat">
            <div class="hn-stat-head"><div><span class="hn-stat-label">إجمالي الحركات</span><div class="hn-stat-value">{{ number_format($summary->transactions_count ?? 0) }}</div></div><span class="hn-stat-icon"><i class="fe fe-list"></i></span></div>
        </article>
        <article class="hn-stat hn-stat-success">
            <div class="hn-stat-head"><div><span class="hn-stat-label">إجمالي الإضافات</span><div class="hn-stat-value">{{ number_format($summary->additions ?? 0, 2) }}</div></div><span class="hn-stat-icon"><i class="fe fe-plus-circle"></i></span></div>
        </article>
        <article class="hn-stat hn-stat-danger">
            <div class="hn-stat-head"><div><span class="hn-stat-label">إجمالي المسحوبات</span><div class="hn-stat-value">{{ number_format($summary->withdrawals ?? 0, 2) }}</div></div><span class="hn-stat-icon"><i class="fe fe-minus-circle"></i></span></div>
        </article>
        <article class="hn-stat hn-stat-warning">
            <div class="hn-stat-head"><div><span class="hn-stat-label">صافي الحركات</span><div class="hn-stat-value">{{ number_format(($summary->additions ?? 0) - ($summary->withdrawals ?? 0), 2) }}</div></div><span class="hn-stat-icon"><i class="fe fe-credit-card"></i></span></div>
        </article>
    </div>

    <section class="hn-panel">
        <div class="hn-panel-header">
            <div><h2 class="hn-panel-title">سجل الحركات</h2><p class="hn-panel-subtitle">أحدث العمليات المسجلة أولًا</p></div>
        </div>

        @if($transactions->isEmpty())
            <x-ui.empty title="لا توجد حركات مالية" description="لا توجد بيانات مسجلة في جدول المحافظ حتى الآن." icon="fe-credit-card" />
        @else
            <div class="hn-table-responsive">
                <table class="hn-table">
                    <thead><tr><th>رقم الحركة</th><th>التاريخ</th><th>النوع</th><th>القيمة</th><th>الرصيد السابق</th><th>الرصيد بعد الحركة</th></tr></thead>
                    <tbody>
                    @foreach($transactions as $transaction)
                        @php
                            $isAddition = (string) $transaction->statue === '0';
                            $result = $isAddition
                                ? (float) $transaction->prev_value + (float) $transaction->value_changing
                                : (float) $transaction->prev_value - (float) $transaction->value_changing;
                        @endphp
                        <tr>
                            <td>#{{ $transaction->id }}</td>
                            <td>{{ optional($transaction->created_at)->format('Y-m-d H:i') ?: '—' }}</td>
                            <td><span class="hn-badge {{ $isAddition ? 'hn-badge-success' : 'hn-badge-danger' }}">{{ $isAddition ? 'إضافة' : 'سحب' }}</span></td>
                            <td><strong>{{ number_format((float) $transaction->value_changing, 2) }}</strong></td>
                            <td>{{ number_format((float) $transaction->prev_value, 2) }}</td>
                            <td>{{ number_format($result, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="hn-pagination">{{ $transactions->withQueryString()->links() }}</div>
        @endif
    </section>
@endsection
