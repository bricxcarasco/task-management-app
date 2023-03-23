@extends('layouts.guest')

@section('content')
<div class="container py-5 py-sm-6 py-md-7">
    <div class="row justify-content-center pt-4">
        <div class="col-lg-7 col-md-9 col-sm-11">
            <h1 class="h2 pb-3">{{ __('Header Reset Email Address Completion') }}</h1>
            <p class="fs-sm">{{ __('You have successfully changed your email address.') }}</p>
            <p class="fs-sm">{{ __('Please login with your new email address.') }}</p>
            <div class="border-top text-center mt-4 pt-4">
                <div class="row pt-2">
                    <div class="col-sm-6 offset-sm-3">
                        <a class="btn btn-primary d-block w-100 mb-3" href="{{ route('login.get') }}">{{ __('Login Page Redirect') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
