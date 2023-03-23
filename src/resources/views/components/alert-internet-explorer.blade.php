@if (Session::has('message'))
    <div class="alert-fixed">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ Session::get('message') }}
        </div>
    </div>
@endif
