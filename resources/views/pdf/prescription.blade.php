<!doctype html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;color:#17324d;font-size:13px}.header{border-bottom:2px solid #159a9c;padding-bottom:12px;margin-bottom:18px}.meta{width:100%;margin-bottom:16px}.meta td{padding:5px}.rx{width:100%;border-collapse:collapse}.rx th,.rx td{border:1px solid #d7e2ea;padding:8px;text-align:right}.rx th{background:#eef7f7}.notes{margin-top:18px;padding:12px;background:#f6f9fb}</style></head>
<body>
<div class="header"><h1>الوصفة الطبية</h1><div>HealthNexus — رقم الوصفة #{{ $prescription->id }}</div></div>
<table class="meta"><tr><td><strong>المريض:</strong> {{ trim($patient->f_name.' '.$patient->l_name) }}</td><td><strong>تاريخ الإصدار:</strong> {{ optional($prescription->issued_at)->format('Y/m/d H:i') }}</td></tr><tr><td><strong>رقم الزيارة:</strong> #{{ $visit->id }}</td><td><strong>رقم الطبيب:</strong> #{{ $prescription->doctor_id }}</td></tr></table>
<table class="rx"><thead><tr><th>الدواء</th><th>الجرعة</th><th>التكرار</th><th>المدة</th><th>طريقة الاستخدام</th></tr></thead><tbody>@foreach($prescription->items as $item)<tr><td>{{ $item->medication_name }}</td><td>{{ $item->dosage }}</td><td>{{ $item->frequency }}</td><td>{{ $item->duration }}</td><td>{{ $item->route }} @if($item->instructions)<br>{{ $item->instructions }}@endif</td></tr>@endforeach</tbody></table>
@if($prescription->notes)<div class="notes"><strong>ملاحظات الطبيب:</strong><br>{{ $prescription->notes }}</div>@endif
</body></html>
