@props(['title', 'description' => null])
<div class="hn-page-heading">
    <div>
        <h1>{{ $title }}</h1>
        @if($description)<p>{{ $description }}</p>@endif
    </div>
    @if(trim($slot))<div class="hn-actions">{{ $slot }}</div>@endif
</div>
