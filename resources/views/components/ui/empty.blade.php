@props(['title' => 'لا توجد بيانات', 'description' => null, 'icon' => 'fe-inbox'])
<div class="hn-empty">
    <i class="fe {{ $icon }} tx-30 d-block mb-2"></i>
    <strong class="d-block mb-1">{{ $title }}</strong>
    @if($description)<span>{{ $description }}</span>@endif
</div>
