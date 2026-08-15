@props(['calendar', 'title' => 'تقويم المواعيد', 'url' => null])
<section class="hn-panel">
    <div class="hn-panel-header"><div><h2 class="hn-panel-title">{{ $title }}</h2><p class="hn-panel-subtitle">{{ $calendar['label'] }}</p></div>@if($url)<a href="{{ $url }}" class="tx-13">عرض المواعيد</a>@endif</div>
    <div class="hn-panel-body">
        <div class="hn-calendar-week"><span>س</span><span>ح</span><span>ن</span><span>ث</span><span>ر</span><span>خ</span><span>ج</span></div>
        <div class="hn-calendar-grid">
            @for($i=0;$i<$calendar['leading'];$i++)<span class="is-empty"></span>@endfor
            @foreach($calendar['days'] as $day)
                <a href="{{ $url ? $url.'?date='.$day['date'] : '#' }}" class="hn-calendar-day {{ $day['today'] ? 'is-today' : '' }} {{ $day['count'] ? 'has-events' : '' }}"><b>{{ $day['day'] }}</b>@if($day['count'])<small>{{ $day['count'] }} موعد</small>@endif</a>
            @endforeach
        </div>
    </div>
</section>
