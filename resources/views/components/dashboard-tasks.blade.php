@props(['tasks', 'title' => 'مركز المهام'])
<section class="hn-panel">
    <div class="hn-panel-header"><div><h2 class="hn-panel-title">{{ $title }}</h2><p class="hn-panel-subtitle">عمليات تحتاج إلى متابعة</p></div><span class="hn-badge {{ $tasks->count() ? 'hn-badge-pending' : 'hn-badge-success' }}">{{ $tasks->count() ? $tasks->count().' أنواع' : 'لا توجد مهام' }}</span></div>
    <div class="hn-panel-body hn-task-center">
        @forelse($tasks as $task)<a href="{{ $task['url'] }}" class="hn-task-item is-{{ $task['tone'] }}"><span class="hn-task-icon"><i class="fe {{ $task['icon'] }}"></i></span><span><strong>{{ $task['label'] }}</strong><small>اضغط للمتابعة</small></span><b>{{ number_format($task['count']) }}</b></a>@empty<x-ui.empty title="لا توجد مهام معلقة" description="جميع العمليات الحالية تمت متابعتها." icon="fe-check-circle" />@endforelse
    </div>
</section>
