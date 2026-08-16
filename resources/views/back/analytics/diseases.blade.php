@extends('layouts.master')

@section('title', 'تحليل الأمراض')

@section('css')
<link href="{{ URL::asset('assets/plugins/leaflet/leaflet.css') }}" rel="stylesheet">
@endsection

@section('content')
<x-ui.page-header title="تحليل انتشار الأمراض" description="تحليل تشخيصات ICD-10 المسجلة للمرضى حسب المدينة والمنطقة والفترة الزمنية." />

<section class="hn-panel mb-4">
    <div class="hn-panel-body">
        <form method="GET" action="{{ route('analytics.diseases') }}" class="hn-analytics-filters" id="disease-analysis-form">
            <div class="hn-filter-disease"><label for="analytics-disease">المرض</label><select id="analytics-disease" name="disease" class="form-control" required>@foreach($diseases as $disease)<option value="{{ $disease->id }}" @selected((string)$filters['disease'] === (string)$disease->id)>{{ $disease->code }} — {{ $disease->name_ar ?: $disease->name_en }}</option>@endforeach</select></div>
            <div><label for="analytics-city">المحافظة / المدينة</label><select id="analytics-city" name="city" class="form-control"><option value="">كل المحافظات</option>@foreach($cities as $city)<option value="{{ $city->id }}" @selected((string)$filters['city'] === (string)$city->id)>{{ trim($city->name) }}</option>@endforeach</select></div>
            <div><label for="analytics-area">المنطقة</label><select id="analytics-area" name="area" class="form-control"><option value="">كل المناطق</option>@foreach($areas as $area)<option value="{{ $area->id }}" data-city="{{ $area->city }}" @selected((string)$filters['area'] === (string)$area->id)>{{ $area->name }}</option>@endforeach</select></div>
            <div><label for="analytics-sex">الجنس</label><select id="analytics-sex" name="sex" class="form-control"><option value="">الجميع</option><option value="1" @selected((string)$filters['sex'] === '1')>ذكر</option><option value="2" @selected((string)$filters['sex'] === '2')>أنثى</option></select></div>
            <div><label for="analytics-age">الفئة العمرية</label><select id="analytics-age" name="age_group" class="form-control"><option value="">كل الأعمار</option><option value="child" @selected($filters['age_group']==='child')>أطفال (0–12)</option><option value="youth" @selected($filters['age_group']==='youth')>شباب (13–24)</option><option value="adult" @selected($filters['age_group']==='adult')>بالغون (25–44)</option><option value="middle" @selected($filters['age_group']==='middle')>متوسطو العمر (45–64)</option><option value="senior" @selected($filters['age_group']==='senior')>كبار السن (65+)</option></select></div>
            <div><label for="analytics-from">من تاريخ</label><input id="analytics-from" type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}"></div>
            <div><label for="analytics-to">إلى تاريخ</label><input id="analytics-to" type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}"></div>
            <div class="hn-filter-actions"><button class="hn-btn hn-btn-primary" type="submit"><i class="fe fe-search"></i> تحليل</button><a class="hn-btn hn-btn-light" href="{{ route('analytics.diseases') }}">مسح</a></div>
        </form>
        @if($selectedDisease ?? false)<form method="POST" action="{{ route('analytics.diseases.export') }}" class="mt-3">@csrf @foreach($filters as $key=>$value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach<button class="hn-btn hn-btn-light" type="submit"><i class="fe fe-download"></i> تصدير النتائج CSV</button></form>@endif
    </div>
</section>

@if(!$selectedDisease)
    <x-ui.empty title="لا توجد تشخيصات قابلة للتحليل" description="تظهر النتائج بعد تسجيل تشخيص ICD-10 في ملف طبي لمريض." icon="fe-activity" />
@else
<div class="hn-analysis-title">
    <div><span class="hn-badge hn-badge-success">{{ $selectedDisease->code }}</span><h2>{{ $selectedDisease->name_ar ?: $selectedDisease->name_en }}</h2></div>
    <p>تعتمد النتائج على المرضى الفريدين، ولا تكرر المريض عند تسجيل التشخيص نفسه أكثر من مرة.</p>
</div>

<div class="hn-stats">
    <article class="hn-stat"><div class="hn-stat-head"><div><span class="hn-stat-label">المرضى المصابون</span><div class="hn-stat-value">{{ number_format($summary['patients']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-users"></i></span></div><div class="hn-stat-note">مرضى فريدون ضمن الفلاتر</div></article>
    <article class="hn-stat hn-stat-success"><div class="hn-stat-head"><div><span class="hn-stat-label">سجلات التشخيص</span><div class="hn-stat-value">{{ number_format($summary['records']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-file-text"></i></span></div><div class="hn-stat-note">يشمل تكرار زيارات المريض</div></article>
    <article class="hn-stat hn-stat-warning"><div class="hn-stat-head"><div><span class="hn-stat-label">المحافظات المتأثرة</span><div class="hn-stat-value">{{ number_format($summary['cities']) }}</div></div><span class="hn-stat-icon"><i class="fe fe-map-pin"></i></span></div><div class="hn-stat-note">وفق عناوين المرضى المسجلة</div></article>
    <article class="hn-stat hn-stat-danger"><div class="hn-stat-head"><div><span class="hn-stat-label">النسبة ضمن المرضى</span><div class="hn-stat-value">{{ number_format($summary['prevalence'], 2) }}%</div></div><span class="hn-stat-icon"><i class="fe fe-trending-up"></i></span></div><div class="hn-stat-note">من أصل {{ number_format($summary['population']) }} مريض ضمن النطاق</div></article>
</div>

@if($comparison['available'])
<div class="hn-comparison {{ $comparison['change'] > 0 ? 'is-up' : 'is-down' }}"><div><span class="hn-section-icon"><i class="fe fe-{{ $comparison['change'] > 0 ? 'trending-up' : 'trending-down' }}"></i></span><div><strong>مقارنة بالفترة السابقة</strong><small>{{ $comparison['from'] }} — {{ $comparison['to'] }}</small></div></div><p><strong>{{ $comparison['change'] > 0 ? '+' : '' }}{{ number_format($comparison['change'], 1) }}%</strong><span>{{ number_format($comparison['previous']) }} مريض في الفترة السابقة</span></p></div>
@endif

<div class="hn-analytics-grid">
    <section class="hn-panel">
        <div class="hn-panel-header"><div><h2 class="hn-panel-title">خريطة انتشار المرض</h2><p class="hn-panel-subtitle">بدّل بين دوائر الحالات والتلوين النسبي للمحافظات.</p></div></div>
        <div class="hn-panel-body">
            <div class="hn-map-toolbar">
                <div class="hn-map-mode" role="group" aria-label="نوع الخريطة">
                    <button type="button" class="hn-btn hn-btn-light is-active" id="map-mode-circles"><i class="fe fe-map-pin"></i> دوائر الانتشار</button>
                    <button type="button" class="hn-btn hn-btn-light" id="map-mode-choropleth"><i class="fe fe-map"></i> تلوين المحافظات</button>
                </div>
                <button type="button" class="hn-btn hn-btn-light is-active" id="map-show-syria"><i class="fe fe-maximize-2"></i> عرض سورية بالكامل</button>
                <button type="button" class="hn-btn hn-btn-light" id="map-show-governorate" @disabled(!$filters['city'])><i class="fe fe-map-pin"></i> عرض المحافظة المختارة</button>
            </div>
            <div class="hn-map-workspace">
                <div class="hn-map-canvas">
                    <div class="hn-syria-map" id="disease-map" aria-label="خريطة تفاعلية لانتشار المرض في سورية"></div>
                    <div class="hn-syria-map d-none" id="disease-choropleth-map" aria-label="خريطة لونية لانتشار المرض حسب محافظات سورية"></div>
                </div>
                <aside class="hn-map-details" id="map-details" aria-live="polite">
                    <div class="hn-map-details-empty" id="map-details-empty"><span class="hn-section-icon"><i class="fe fe-map-pin"></i></span><h3>تفاصيل المحافظة</h3><p>اضغط على محافظة أو دائرة لعرض مؤشرات الانتشار والتوزيع السكاني.</p></div>
                    <div class="d-none" id="map-details-content">
                        <div class="hn-map-details-head"><div><small>المحافظة المحددة</small><h3 id="detail-name">—</h3></div><button type="button" class="hn-icon-btn" id="detail-close" aria-label="إغلاق التفاصيل"><i class="fe fe-x"></i></button></div>
                        <div class="hn-map-detail-kpis"><div><span>المرضى</span><strong id="detail-patients">0</strong></div><div><span>لكل 1000</span><strong id="detail-rate">0</strong></div><div><span>من الحالات</span><strong id="detail-share">0%</strong></div><div><span>الترتيب</span><strong id="detail-rank">—</strong></div></div>
                        <div class="hn-map-comparison d-none" id="detail-comparison"><span>مقارنة بالفترة السابقة</span><strong id="detail-change">—</strong><small id="detail-previous"></small></div>
                        <div class="hn-map-detail-section"><h4>المناطق الأعلى</h4><div id="detail-areas"></div></div>
                        <div class="hn-map-detail-section"><h4>حسب الجنس</h4><div id="detail-genders"></div></div>
                        <div class="hn-map-detail-section"><h4>حسب العمر</h4><div id="detail-ages"></div></div>
                        <div class="hn-map-detail-actions"><button type="button" class="hn-btn hn-btn-primary" id="detail-apply"><i class="fe fe-filter"></i> تطبيق كفلتر</button><button type="button" class="hn-btn hn-btn-light" id="detail-reset"><i class="fe fe-maximize-2"></i> العودة إلى سورية</button></div>
                    </div>
                </aside>
            </div>
            <div class="hn-map-legend" id="circle-map-legend"><span><i></i> حجم الدائرة يعكس عدد المرضى</span><small><i class="fe fe-move ml-1"></i> اضغط على الدائرة للتفاصيل.</small></div>
            <div class="hn-map-legend hn-color-legend d-none" id="choropleth-map-legend"><span id="color-legend-values"></span><small>يعتمد اللون على عدد المرضى ضمن الفلاتر الحالية.</small></div>
            <small class="hn-map-source d-none" id="choropleth-map-source">حدود المحافظات: geoBoundaries / UN OCHA — CC BY 3.0 IGO.</small>
        </div>
    </section>

    <section class="hn-panel">
        <div class="hn-panel-header"><div><h2 class="hn-panel-title">التوزيع حسب المحافظة</h2><p class="hn-panel-subtitle">المرضى الفريدون مرتبين من الأعلى.</p></div></div>
        <div class="hn-panel-body hn-location-ranking">
            @forelse($cityDistribution as $location)
                <button type="button" class="hn-report-row hn-map-location" data-city-id="{{ $location->city_id }}"><div><strong>{{ $location->location_name }}</strong><span>{{ $location->patients_count < 5 ? 'أقل من 5' : number_format($location->patients_count).' مريض' }} · {{ number_format($location->rate_per_1000, 2) }} لكل 1000</span></div><div class="hn-report-bar"><span style="width:{{ $cityDistribution->max('patients_count') ? max(4, round(($location->patients_count / $cityDistribution->max('patients_count')) * 100)) : 0 }}%"></span></div></button>
            @empty <x-ui.empty title="لا توجد نتائج" description="غيّر المرض أو النطاق الجغرافي أو الفترة." icon="fe-map-pin" /> @endforelse
        </div>
    </section>
</div>

<div class="hn-grid mt-4">
    <section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title">التوزيع حسب العمر</h2><p class="hn-panel-subtitle">الفئات العمرية الأكثر تأثرًا.</p></div></div><div class="hn-panel-body">@forelse($ageDistribution as $age)<div class="hn-report-row"><div><strong>{{ $age->age_group }}</strong><span>{{ number_format($age->patients_count) }} مريض</span></div><div class="hn-report-bar"><span style="width:{{ $ageDistribution->max('patients_count') ? max(4,round($age->patients_count*100/$ageDistribution->max('patients_count'))) : 0 }}%"></span></div></div>@empty<x-ui.empty title="لا توجد بيانات عمرية" description="لا توجد نتائج ضمن الفلاتر الحالية." icon="fe-users" />@endforelse</div></section>
    <section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title">جودة بيانات التحليل</h2><p class="hn-panel-subtitle">مؤشرات تساعد على تحسين دقة النتائج.</p></div></div><div class="hn-panel-body hn-quality-list"><div><span>اكتمال ترميز ICD-10</span><strong>{{ number_format($quality['coding_rate'],1) }}%</strong></div><div><span>مرضى دون موقع مكتمل</span><strong>{{ number_format($quality['missing_location']) }}</strong></div><div><span>تشخيصات دون تاريخ صالح</span><strong>{{ number_format($quality['missing_dates']) }}</strong></div><div><span>تشخيصات نصية تحتاج ترميزًا</span><strong>{{ number_format($quality['free_text']) }}</strong></div></div></section>
</div>

<div class="hn-grid mt-4">
    <section class="hn-panel">
        <div class="hn-panel-header"><div><h2 class="hn-panel-title">اتجاه الحالات زمنيًا</h2><p class="hn-panel-subtitle">عدد المرضى الفريدين لكل شهر.</p></div></div>
        <div class="hn-panel-body"><div class="hn-trend-chart">
            @forelse($trend as $point)<div class="hn-trend-item" title="{{ $point->period }}: {{ $point->patients_count }}"><span style="height:{{ $trend->max('patients_count') ? max(5, round(($point->patients_count / $trend->max('patients_count')) * 100)) : 0 }}%"></span><small>{{ substr($point->period, 2) }}</small></div>@empty <x-ui.empty title="لا يوجد اتجاه زمني" description="بعض التشخيصات القديمة لا تحتوي تاريخًا صالحًا." icon="fe-bar-chart" /> @endforelse
        </div></div>
    </section>
    <section class="hn-panel">
        <div class="hn-panel-header"><div><h2 class="hn-panel-title">التوزيع حسب الجنس</h2><p class="hn-panel-subtitle">وفق بيانات ملفات المرضى.</p></div></div>
        <div class="hn-panel-body">@forelse($genderDistribution as $gender)<div class="hn-ranked-item"><span>{{ $loop->iteration }}</span><div><strong>{{ $gender->gender }}</strong><small>{{ number_format($gender->patients_count) }} مريض</small></div></div>@empty <x-ui.empty title="لا توجد بيانات" description="لا توجد نتائج ضمن الفلاتر الحالية." icon="fe-users" /> @endforelse</div>
    </section>
</div>

<section class="hn-panel mt-4">
    <div class="hn-panel-header"><div><h2 class="hn-panel-title">المناطق الأعلى انتشارًا</h2><p class="hn-panel-subtitle">أعلى 12 منطقة ضمن النتائج الحالية.</p></div></div>
    @if($areaDistribution->isEmpty())<x-ui.empty title="لا توجد مناطق" description="بيانات المنطقة غير متوفرة للنتائج الحالية." icon="fe-map" />@else
    <div class="hn-table-responsive"><table class="hn-table"><thead><tr><th>الترتيب</th><th>المنطقة</th><th>المحافظة</th><th>عدد المرضى</th><th>النسبة من الحالات</th></tr></thead><tbody>@foreach($areaDistribution as $area)<tr><td>#{{ $loop->iteration }}</td><td><strong>{{ $area->area_name }}</strong></td><td>{{ $area->city_name }}</td><td>{{ $area->patients_count < 5 ? 'أقل من 5' : number_format($area->patients_count) }}</td><td>{{ $area->patients_count < 5 ? '—' : ($summary['patients'] ? number_format(($area->patients_count / $summary['patients']) * 100, 1).'%' : '0%') }}</td></tr>@endforeach</tbody></table></div>@endif
</section>
@endif
@endsection

@section('js')
<script src="{{ URL::asset('assets/plugins/leaflet/leaflet.js') }}"></script>
<script>
(function () {
    var citySelect = document.getElementById('analytics-city');
    var areaSelect = document.getElementById('analytics-area');
    function filterAreas() {
        var city = citySelect.value;
        Array.prototype.forEach.call(areaSelect.options, function (option, index) {
            if (!index) return;
            option.hidden = !!city && option.dataset.city !== city;
        });
        if (areaSelect.selectedOptions[0] && areaSelect.selectedOptions[0].hidden) areaSelect.value = '';
    }
    citySelect.addEventListener('change', filterAreas);
    filterAreas();

    var positions = {
        'دمشق':[33.5138,36.2765], 'ريف دمشق':[33.58,36.62], 'القنيطرة':[33.125,35.824], 'درعا':[32.618,36.102], 'السويداء':[32.709,36.57],
        'حمص':[34.73,36.71], 'حماة':[35.13,36.75], 'طرطوس':[34.89,35.88], 'اللاذقية':[35.53,35.79], 'إدلب':[35.93,36.63],
        'حلب':[36.20,37.16], 'الرقة':[35.95,39.01], 'دير الزور':[35.34,40.14], 'الحسكة':[36.50,40.75], 'القامشلي':[37.05,41.22]
    };
    var data = @json($cityDistribution ?? []);
    var mapDetails = @json($mapDetails ?? []);
    var selectedDetailId = null;
    var detailsEmpty = document.getElementById('map-details-empty');
    var detailsContent = document.getElementById('map-details-content');
    function formatCount(value) { return Number(value || 0).toLocaleString('ar'); }
    function renderBreakdown(containerId, rows, total) {
        var container = document.getElementById(containerId); container.textContent = '';
        if (!rows || !rows.length) { var empty = document.createElement('small'); empty.className = 'text-muted'; empty.textContent = 'لا توجد بيانات مكتملة'; container.appendChild(empty); return; }
        rows.forEach(function (row) {
            var item = document.createElement('div'); item.className = 'hn-map-breakdown';
            var label = document.createElement('span'); label.textContent = row.label;
            var value = document.createElement('strong'); value.textContent = Number(row.count) < 5 ? 'أقل من 5' : formatCount(row.count);
            var bar = document.createElement('i'); bar.style.width = Math.max(4, Math.round(Number(row.count) * 100 / Math.max(1, total))) + '%';
            item.appendChild(label); item.appendChild(value); item.appendChild(bar); container.appendChild(item);
        });
    }
    function highlightSelection(cityId) {
        Object.keys(governorateLayers || {}).forEach(function (id) {
            var selected = String(id) === String(cityId);
            governorateLayers[id].setStyle({fillOpacity:selected ? 1 : .25, opacity:selected ? 1 : .45, weight:selected ? 3 : 1.2, color:selected ? '#17333d' : '#fff'});
        });
        Object.keys(markers || {}).forEach(function (id) { markers[id].marker.setStyle({fillOpacity:String(id) === String(cityId) ? 1 : .28, opacity:String(id) === String(cityId) ? 1 : .45}); });
    }
    function selectGovernorate(cityId, zoom) {
        var detail = mapDetails[String(cityId)]; if (!detail) return;
        selectedDetailId = String(cityId); detailsEmpty.classList.add('d-none'); detailsContent.classList.remove('d-none');
        document.getElementById('detail-name').textContent = detail.name;
        document.getElementById('detail-patients').textContent = Number(detail.patients) < 5 ? 'أقل من 5' : formatCount(detail.patients);
        document.getElementById('detail-rate').textContent = Number(detail.rate).toLocaleString('ar');
        document.getElementById('detail-share').textContent = Number(detail.share).toLocaleString('ar') + '%';
        document.getElementById('detail-rank').textContent = '#' + Number(detail.rank).toLocaleString('ar');
        var comparisonBox = document.getElementById('detail-comparison');
        comparisonBox.classList.toggle('d-none', detail.change === null || typeof detail.change === 'undefined');
        if (detail.change !== null && typeof detail.change !== 'undefined') {
            document.getElementById('detail-change').textContent = (Number(detail.change) > 0 ? '+' : '') + Number(detail.change).toLocaleString('ar') + '%';
            document.getElementById('detail-previous').textContent = formatCount(detail.previous) + ' مريض في الفترة السابقة';
            comparisonBox.classList.toggle('is-up', Number(detail.change) > 0); comparisonBox.classList.toggle('is-down', Number(detail.change) <= 0);
        }
        renderBreakdown('detail-areas', detail.areas, detail.patients); renderBreakdown('detail-genders', detail.genders, detail.patients); renderBreakdown('detail-ages', detail.ages, detail.patients);
        highlightSelection(cityId);
        document.querySelectorAll('.hn-map-location').forEach(function (row) { row.classList.toggle('is-active', String(row.dataset.cityId) === String(cityId)); });
        if (zoom) {
            if (activeMapMode === 'choropleth' && governorateLayers[cityId]) choroplethMap.fitBounds(governorateLayers[cityId].getBounds(), {padding:[28,28], maxZoom:9});
            else if (markers[cityId]) map.flyTo(markers[cityId].point, 9, {duration:.7});
            setToolbar('governorate');
        }
    }
    function clearGovernorateSelection() {
        selectedDetailId = null; detailsContent.classList.add('d-none'); detailsEmpty.classList.remove('d-none');
        Object.keys(governorateLayers || {}).forEach(function (id) { governorateLayers[id].setStyle({fillOpacity:.92, opacity:1, weight:1.5, color:'#fff'}); });
        Object.keys(markers || {}).forEach(function (id) { markers[id].marker.setStyle({fillOpacity:.82, opacity:1}); });
        document.querySelectorAll('.hn-map-location').forEach(function (row) { row.classList.remove('is-active'); });
        displaySyria();
    }
    var mapElement = document.getElementById('disease-map');
    var choroplethElement = document.getElementById('disease-choropleth-map');
    if (!mapElement || typeof L === 'undefined') return;
    var syriaBounds = L.latLngBounds([[32.30,35.55],[37.35,42.40]]);
    var map = L.map(mapElement, {zoomControl: false, minZoom: 6, maxZoom: 12, scrollWheelZoom: false, maxBounds: [[31.7,34.8],[38.0,43.0]], maxBoundsViscosity:.7});
    L.control.zoom({position: 'topright', zoomInTitle: 'تكبير', zoomOutTitle: 'تصغير'}).addTo(map);
    L.control.scale({position: 'bottomleft', imperial: false}).addTo(map);
    var tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 12,
        attribution: '&copy; OpenStreetMap',
        crossOrigin: true
    });
    tiles.on('tileerror', function () { mapElement.classList.add('is-offline'); });
    tiles.addTo(map);
    var max = Math.max.apply(null, data.map(function (item) { return Number(item.patients_count); }).concat([1]));
    var markers = {};
    data.forEach(function (item) {
        if (Number(item.patients_count) < 5) return;
        var name = String(item.location_name || '').trim();
        var point = positions[name];
        if (!point) return;
        var radius = 9 + Math.sqrt(Number(item.patients_count) / max) * 22;
        var marker = L.circleMarker(point, {radius: radius, color: '#fff', weight: 3, fillColor: '#c94b58', fillOpacity: .82});
        var popup = document.createElement('div'); popup.className = 'hn-map-popup';
        var title = document.createElement('strong'); title.textContent = name;
        var count = document.createElement('span'); count.textContent = Number(item.patients_count).toLocaleString('ar') + ' مريض';
        var rate = document.createElement('small'); rate.textContent = Number(item.rate_per_1000).toLocaleString('ar') + ' حالة لكل 1000 مريض';
        popup.appendChild(title); popup.appendChild(count); popup.appendChild(rate);
        marker.bindTooltip(name, {direction:'top', className:'hn-map-tooltip'}).bindPopup(popup).addTo(map);
        marker.on('click', function () {
            selectGovernorate(item.city_id, false);
            var row = document.querySelector('.hn-map-location[data-city-id="' + item.city_id + '"]'); if (row) row.scrollIntoView({behavior:'smooth', block:'nearest'});
        });
        markers[item.city_id] = {marker: marker, point: point};
    });
    document.querySelectorAll('.hn-map-location').forEach(function (row) {
        row.addEventListener('click', function () {
            if (activeMapMode === 'choropleth' && governorateLayers[row.dataset.cityId]) {
                var layer = governorateLayers[row.dataset.cityId];
                choroplethMap.fitBounds(layer.getBounds(), {padding:[28,28], maxZoom:9}); layer.openPopup();
            } else {
                var target = markers[row.dataset.cityId]; if (!target) return;
                map.flyTo(target.point, 9, {duration:.7}); target.marker.openPopup();
            }
            selectGovernorate(row.dataset.cityId, false);
        });
    });
    var selectedCityId = @json($filters['city']);
    var activeMapMode = 'circles';
    var choroplethMap = L.map(choroplethElement, {zoomControl:false, minZoom:6, maxZoom:11, scrollWheelZoom:false, attributionControl:false});
    L.control.zoom({position:'topright', zoomInTitle:'تكبير', zoomOutTitle:'تصغير'}).addTo(choroplethMap);
    var governorateLayers = {};
    var dataByArabicName = {};
    data.forEach(function (item) { dataByArabicName[String(item.location_name || '').trim()] = item; });
    var englishToArabic = {
        'Damascus':'دمشق', 'Aleppo':'حلب', 'Rural Damascus':'ريف دمشق', 'Homs':'حمص', 'Hama':'حماة',
        'Lattakia':'اللاذقية', 'Idleb':'إدلب', 'Al-Hasakeh':'الحسكة', 'Deir-ez-Zor':'دير الزور',
        'Tartous':'طرطوس', 'Ar-Raqqa':'الرقة', "Dar'a":'درعا', 'As-Sweida':'السويداء', 'Quneitra':'القنيطرة'
    };
    function governorateColor(count) {
        var ratio = Number(count || 0) / max;
        if (!count) return '#e8f0f3';
        if (ratio >= .75) return '#075da8';
        if (ratio >= .5) return '#1685d1';
        if (ratio >= .25) return '#4aa5ea';
        if (ratio >= .1) return '#82c3f2';
        return '#b9ddf7';
    }
    var legendValues = document.getElementById('color-legend-values');
    [0, .1, .25, .5, .75].forEach(function (ratio, index, values) {
        var part = document.createElement('span'); part.className = 'hn-color-step';
        var swatch = document.createElement('b'); swatch.style.background = governorateColor(Math.max(1, max * ratio));
        var from = Math.round(max * ratio); var to = index < values.length - 1 ? Math.max(from, Math.round(max * values[index + 1]) - 1) : null;
        var label = document.createElement('small'); label.textContent = to === null ? from + '+' : from + '–' + to;
        part.appendChild(swatch); part.appendChild(label); legendValues.appendChild(part);
    });
    function popupContent(name, item) {
        var wrapper = document.createElement('div'); wrapper.className = 'hn-map-popup';
        var title = document.createElement('strong'); title.textContent = name;
        var count = document.createElement('span');
        count.textContent = !item ? 'لا توجد حالات ضمن الفلاتر' : (Number(item.patients_count) < 5 ? 'أقل من 5 مرضى' : Number(item.patients_count).toLocaleString('ar') + ' مريض');
        wrapper.appendChild(title); wrapper.appendChild(count);
        if (item) { var rate = document.createElement('small'); rate.textContent = Number(item.rate_per_1000).toLocaleString('ar') + ' حالة لكل 1000 مريض'; wrapper.appendChild(rate); }
        return wrapper;
    }
    fetch(@json(asset('assets/data/syria-governorates.geojson')))
        .then(function (response) { if (!response.ok) throw new Error('GeoJSON'); return response.json(); })
        .then(function (geojson) {
            L.geoJSON(geojson, {
                style: function (feature) {
                    var name = englishToArabic[feature.properties.shapeName] || feature.properties.shapeName;
                    var item = dataByArabicName[name];
                    return {color:'#fff', weight:1.5, fillColor:governorateColor(item && item.patients_count), fillOpacity:.92};
                },
                onEachFeature: function (feature, layer) {
                    var name = englishToArabic[feature.properties.shapeName] || feature.properties.shapeName;
                    var item = dataByArabicName[name];
                    layer.bindTooltip(name, {permanent:true, direction:'center', className:'hn-map-label'}).bindPopup(popupContent(name, item));
                    layer.on({
                        mouseover:function () { layer.setStyle({weight:3, color:'#17333d'}); layer.bringToFront(); },
                        mouseout:function () { highlightSelection(selectedDetailId); if (!selectedDetailId) layer.setStyle({weight:1.5, color:'#fff', fillOpacity:.92, opacity:1}); },
                        click:function () {
                            if (item) selectGovernorate(item.city_id, true);
                        }
                    });
                    if (item) governorateLayers[item.city_id] = layer;
                }
            }).addTo(choroplethMap);
            if (selectedCityId && mapDetails[String(selectedCityId)]) selectGovernorate(selectedCityId, activeMapMode === 'choropleth');
            else if (activeMapMode === 'choropleth') displayCurrentExtent();
        })
        .catch(function () { choroplethElement.classList.add('is-offline'); });
    var showSyria = document.getElementById('map-show-syria');
    var showGovernorate = document.getElementById('map-show-governorate');
    var modeCircles = document.getElementById('map-mode-circles');
    var modeChoropleth = document.getElementById('map-mode-choropleth');
    var circleLegend = document.getElementById('circle-map-legend');
    var choroplethLegend = document.getElementById('choropleth-map-legend');
    var choroplethSource = document.getElementById('choropleth-map-source');
    showGovernorate.disabled = !selectedCityId;
    function setToolbar(active) {
        showSyria.classList.toggle('is-active', active === 'syria');
        showGovernorate.classList.toggle('is-active', active === 'governorate');
    }
    function displaySyria() {
        (activeMapMode === 'choropleth' ? choroplethMap : map).fitBounds(syriaBounds, {padding:[16,16]});
        setToolbar('syria');
    }
    function displayGovernorate() {
        if (activeMapMode === 'choropleth') {
            var layer = governorateLayers[selectedCityId]; if (!layer) return;
            choroplethMap.fitBounds(layer.getBounds(), {padding:[28,28], maxZoom:9}); layer.openPopup();
        } else {
            var target = markers[selectedCityId]; if (!target) return;
            map.flyTo(target.point, 10, {duration:.7}); target.marker.openPopup();
        }
        setToolbar('governorate');
    }
    function displayCurrentExtent() { selectedCityId ? displayGovernorate() : displaySyria(); }
    function setMapMode(mode) {
        activeMapMode = mode;
        var colorMode = mode === 'choropleth';
        mapElement.classList.toggle('d-none', colorMode); choroplethElement.classList.toggle('d-none', !colorMode);
        circleLegend.classList.toggle('d-none', colorMode); choroplethLegend.classList.toggle('d-none', !colorMode); choroplethSource.classList.toggle('d-none', !colorMode);
        modeCircles.classList.toggle('is-active', !colorMode); modeChoropleth.classList.toggle('is-active', colorMode);
        setTimeout(function () { (colorMode ? choroplethMap : map).invalidateSize(); displayCurrentExtent(); }, 50);
    }
    showSyria.addEventListener('click', displaySyria);
    showGovernorate.addEventListener('click', displayGovernorate);
    modeCircles.addEventListener('click', function () { setMapMode('circles'); });
    modeChoropleth.addEventListener('click', function () { setMapMode('choropleth'); });
    document.getElementById('detail-close').addEventListener('click', clearGovernorateSelection);
    document.getElementById('detail-reset').addEventListener('click', clearGovernorateSelection);
    document.getElementById('detail-apply').addEventListener('click', function () {
        if (!selectedDetailId) return; citySelect.value = selectedDetailId; filterAreas(); document.getElementById('disease-analysis-form').submit();
    });
    setTimeout(function () { map.invalidateSize(); if (selectedCityId && mapDetails[String(selectedCityId)]) selectGovernorate(selectedCityId, true); else displaySyria(); }, 100);
})();
</script>
@endsection
