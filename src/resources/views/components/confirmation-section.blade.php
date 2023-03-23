@extends('layouts.main')

@section('content')
<div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
    <div class="row">
        <div class="col-12 col-md-9 offset-md-3">
            <div class="d-flex flex-column h-100 rounded-3 ">
                <div class="position-relative">
                    <div class="tab-content mt-2">
                        <div class="tab-pane fade active show">
                            <div class="position-relative">   
                                <a href="#">
                                    <i class="ai-arrow-left message__back"> {{ __('戻る') }}</i>
                                </a>                                               
                                <h3 class="py-3 mb-0 text-center ">
                                    {{ $title }}
                                </h3>                                        
                            </div>                
                            <!-- Confirmation -->
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
