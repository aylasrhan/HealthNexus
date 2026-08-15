@extends('layouts.master')
@section('title', 'أسئلة المرضى')
@section('content')
<x-ui.page-header title="أسئلة المرضى" description="متابعة استفسارات المرضى والإجابات الطبية."><a href="{{ route('questions.create') }}" class="hn-btn hn-btn-primary"><i class="fe fe-plus"></i> سؤال جديد</a></x-ui.page-header>
<x-ui.flash />
<section class="hn-panel"><div class="hn-panel-header"><div><h2 class="hn-panel-title">الأسئلة والإجابات</h2><p class="hn-panel-subtitle">{{ number_format($questions->count()) }} سؤالًا</p></div></div><div class="hn-panel-body">
@if($questions->isEmpty())<x-ui.empty title="لا توجد أسئلة" description="لم يتم إرسال أسئلة حتى الآن." icon="fe-message-circle" />@else
<div class="hn-faq" id="questions-accordion">@foreach($questions as $index => $question)<article class="hn-faq-item"><button class="hn-faq-question collapsed" type="button" data-toggle="collapse" data-target="#question-{{ $question->id }}" aria-expanded="false" aria-controls="question-{{ $question->id }}"><span><small>سؤال #{{ $question->id }}</small><strong>{{ $question->Question }}</strong></span><i class="fe fe-chevron-down"></i></button><div id="question-{{ $question->id }}" class="collapse" data-parent="#questions-accordion"><div class="hn-faq-answer">@if($question->answer)<p>{{ $question->answer }}</p>@else<span class="hn-badge hn-badge-pending">بانتظار الإجابة</span>@endif<div class="hn-row-actions mt-3"><a href="{{ route('questions.edit', $question->id) }}" class="hn-btn hn-btn-light"><i class="fe fe-edit-2"></i> تعديل</a></div></div></div></article>@endforeach</div>@endif
</div></section>
@endsection
