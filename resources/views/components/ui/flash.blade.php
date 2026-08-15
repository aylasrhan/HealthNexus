@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fe fe-check-circle ml-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fe fe-alert-circle ml-2"></i>تعذر إتمام العملية. يرجى المحاولة مرة أخرى.
        <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button>
    </div>
@endif
