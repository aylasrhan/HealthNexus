@extends('layouts.public')

@section('content')
<section class="hn-public-page-hero"><span>فريقنا الطبي</span><h1>خبرات طبية تهتم بتفاصيلك</h1><p>نخبة من الأطباء ضمن أقسام المركز المختلفة، جاهزون لتقديم الرعاية المناسبة لك ولعائلتك.</p></section>
<section class="hn-public-section"><div class="hn-public-grid hn-doctors-public-grid">@forelse($doctors as $doctor)@include('public.partials.doctor-card', ['doctor' => $doctor])@empty<p class="hn-public-empty">لا توجد بيانات أطباء فعالة للعرض حالياً.</p>@endforelse</div>@if($doctors->hasPages())<div class="hn-public-pagination">{{ $doctors->links() }}</div>@endif</section>
@endsection
