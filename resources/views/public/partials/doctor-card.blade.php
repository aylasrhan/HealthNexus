<article class="hn-public-card hn-public-doctor-card">
    <div class="hn-public-doctor-avatar"><i class="fe fe-user"></i></div>
    <div class="hn-public-doctor-info"><span>{{ $doctor->gnr_m_clinics?->name_ar ?? 'قسم طبي' }}</span><h3>{{ $doctor->name_ar }}</h3><p>{{ $doctor->specialization_ar ?: 'طبيب مختص' }}</p></div>
    <div class="hn-public-doctor-meta"><span><i class="fe fe-clock"></i> {{ $doctor->from_time ? \Carbon\Carbon::parse($doctor->from_time)->format('H:i') : '--:--' }} - {{ $doctor->to_time ? \Carbon\Carbon::parse($doctor->to_time)->format('H:i') : '--:--' }}</span>@if($doctor->famous)<span class="hn-public-badge"><i class="fe fe-star"></i> طبيب متميز</span>@endif</div>
</article>
