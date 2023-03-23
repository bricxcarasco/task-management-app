@extends('layouts.main')

@section('content')    
    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            <div class="col-12 col-md-9 offset-md-3">
                <h3>Test</h3>
                <button type="button" class="btn btn-primary">
                    Payment Method
                </button>
            </div>
        </div>
    </div>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
