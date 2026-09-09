@extends('layouts.public')

@section('content')
<section class="hn-public-page-hero"><span>عن WeCare</span><h1>رعاية صحية أكثر قرباً ووضوحاً</h1><p>نؤمن أن تجربة المريض تبدأ بالاستماع الجيد وتنتهي بمتابعة مستمرة. لذلك يجمع مركز WeCare بين الكوادر المختصة والتنظيم الرقمي لتقديم رعاية آمنة وسهلة الوصول.</p></section>
<section class="hn-public-section hn-about-grid"><article><i class="fe fe-heart"></i><h2>رؤيتنا</h2><p>أن تكون الرعاية الصحية تجربة إنسانية موثوقة، تعتمد على المعرفة والتواصل الواضح مع كل مريض.</p></article><article><i class="fe fe-target"></i><h2>رسالتنا</h2><p>تقديم خدمات طبية متكاملة عبر أقسام متخصصة وفريق طبي مؤهل وإدارة دقيقة للمواعيد والملفات الطبية.</p></article><article><i class="fe fe-shield"></i><h2>قيمنا</h2><p>الخصوصية، الدقة، الاحترام، والعمل بروح الفريق؛ لأنها أساس الثقة في كل رحلة علاج.</p></article></section>
<section class="hn-public-section hn-public-muted"><div class="hn-section-heading"><div><span>WeCare بالأرقام</span><h2>منظومة رعاية تتوسع لخدمتك</h2></div></div><div class="hn-public-stats"><div><strong>{{ $statistics['clinics'] }}</strong><span>قسم وعيادة فعالة</span></div><div><strong>{{ $statistics['doctors'] }}</strong><span>طبيب مختص</span></div><div><strong>{{ $statistics['services'] }}</strong><span>خدمة طبية متاحة</span></div></div></section>
@endsection
