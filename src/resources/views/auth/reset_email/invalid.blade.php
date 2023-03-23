@extends('layouts.guest')

@section('content')
<div class="container py-5 py-sm-6 py-md-7">
    <div class="row justify-content-center pt-4">
        <div class="col-lg-7 col-md-9 col-sm-11">
            <h1 class="h2 pb-3">{{ __('Header Reset Email Address Completion') }}</h1>
            <p class="fs-sm">{{ __('Reset Email Verification Failed') }}</p>
            <div class="border-top text-center mt-4 pt-4">
                <div class="row pt-2">
                    <div class="col-sm-6 offset-sm-3">
                        {{-- Redirect to Rio Information Edit Page --}}
                        @auth
                            <a class="btn btn-primary d-block w-100 mb-3" href="{{ route('rio.information.edit') }}">{{ __('Edit Account Information') }}</a>
                        @endauth

                        {{-- Redirect to Login Page --}}
                        @guest
                            <a class="btn btn-primary d-block w-100 mb-3" href="{{ route('login.get') }}">{{ __('Login Page Redirect') }}</a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
