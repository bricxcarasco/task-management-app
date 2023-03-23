@extends('layouts.guest')

@section('content')
<section class="container d-flex align-items-center pt-7 pb-3 pb-md-4 h-md-100" style="flex: 1 0 auto;">
    <div class="w-100 pt-3 pt-md-0">
        <div class="row justify-content-center pt-4">
            <div class="col-lg-7 col-md-9 col-sm-11">
                <h1 class="h2 pb-3 text-md-center">{{ __('Header Member Registration Completion') }}</h1>
                <div class="bg-white rounded-3 px-3 py-4 p-sm-4">
                    <p class="fs-sm">{{ __('Paragraph Member Registration Completion') }}</p>
                    <div class="border-top text-center mt-4 pt-4">
                        <!-- <p class="fs-sm fw-medium text-heading">{{ __('Sign Up With') }}</p> -->

                        <div class="row pt-2">
                            <div class="col-sm-6">
                                <a class="btn btn-primary d-block w-100 mb-3" href="https://mail.google.com/">{{ __('Gmail Link') }}</a>
                            </div>
                            <div class="col-sm-6">
                                <a class="btn btn-primary d-block w-100 mb-3" href="https://mail.yahoo.com/">{{ __('Yahoo Link') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
