@extends('layouts.public')

@section('content')
<section class="hn-public-page-hero"><span>خدماتنا الطبية</span><h1>كل ما تحتاجه للعناية بصحتك</h1><p>خدمات طبية وتشخيصية موزعة على أقسام المركز لتقديم رحلة علاج منظمة ومتكاملة.</p></section>
<section class="hn-public-section"><div class="hn-public-grid hn-services-public-grid">@forelse($services as $service)@include('public.partials.service-card', ['service' => $service])@empty<p class="hn-public-empty">لا توجد خدمات فعالة للعرض حالياً.</p>@endforelse</div>@if($services->hasPages())<div class="hn-public-pagination">{{ $services->links() }}</div>@endif</section>
@endsection
