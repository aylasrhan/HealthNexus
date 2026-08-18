@extends('layouts.master')
@section('title', 'أسئلة العيادة')
@section('content')
@php
    $pendingOnly = request()->routeIs('questions.answer');
    $sectionName = $questions->first()?->gnr_m_clinics?->name_ar;
@endphp
<x-ui.page-header
    :title="$pendingOnly ? 'الأسئلة التي تنتظر إجابتي' : 'أسئلة العيادة'"
    :description="$sectionName ? 'استفسارات المرضى في قسم '.$sectionName : 'متابعة استفسارات المرضى والإجابة عنها.'"
/>
<x-ui.flash />

<section class="hn-panel">
    <div class="hn-panel-header"><div><h2 class="hn-panel-title">{{ $pendingOnly ? 'بانتظار الإجابة' : 'الأسئلة والإجابات' }}</h2><p class="hn-panel-subtitle">{{ number_format($questions->count()) }} سؤالًا</p></div></div>
    <div class="hn-panel-body">
        @if($questions->isEmpty())
            <x-ui.empty :title="$pendingOnly ? 'لا توجد أسئلة تنتظر إجابتك' : 'لا توجد أسئلة في هذه العيادة'" description="ستظهر الاستفسارات هنا فور إرسالها من المرضى." icon="fe-message-circle" />
        @else
            <div class="hn-faq" id="questions-accordion">
                @foreach($questions as $question)
                    <article class="hn-faq-item">
                        <button class="hn-faq-question collapsed" type="button" data-toggle="collapse" data-target="#question-{{ $question->id }}" aria-expanded="false">
                            <span><small>{{ $question->user?->name ?: 'مستخدم' }} · سؤال #{{ $question->id }}</small><strong>{{ $question->Question }}</strong></span><i class="fe fe-chevron-down"></i>
                        </button>
                        <div id="question-{{ $question->id }}" class="collapse" data-parent="#questions-accordion"><div class="hn-faq-answer">
                            @if($question->answer)<p>{{ $question->answer }}</p>@else<span class="hn-badge hn-badge-pending">بانتظار الإجابة</span>@endif
                          @can('update', $question)
    <!-- نموذج الرد المباشر من نفس الصفحة -->
    <form action="{{ route('questions.reply', $question->id) }}" method="POST" style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
        @csrf
        <div class="form-group">
            <label style="font-weight: bold; color: #0056b3; margin-bottom:8px; display:block;">رد الطبيب:</label>
            <textarea name="answer" class="form-control" rows="3" placeholder="اكتب ردك هنا للمريض..." required>{{ $question->answer }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-2" style="background-color: #0056b3; color: white; border: none; padding: 6px 16px; border-radius: 4px;">
            <i class="fe fe-send"></i> {{ $question->answer ? 'تعديل الإجابة' : 'إرسال الرد' }}
        </button>
    </form>
@endcan
                            <!-- @can('update', $question)<div class="hn-row-actions mt-3"><a href="{{ route('questions.edit', $question->id) }}" class="hn-btn hn-btn-primary"><i class="fe fe-edit-2"></i> {{ $question->answer ? 'تعديل الإجابة' : 'إجابة السؤال' }}</a></div>@endcan -->
                        </div></div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
