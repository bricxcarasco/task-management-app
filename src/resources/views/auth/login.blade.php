@php
    use App\Services\CordovaService;
@endphp

@extends('layouts.guest')

@section('content')
<section class="container d-flex align-items-center pt-7 pb-3 pb-md-4" style="flex: 1 0 auto;">
    <div class="w-100 pt-3">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-12">
                <div class="d-md-flex align-items-center justify-content-between bg-white p-4 py-5" id="signin-view">
                    <div style="flex: 1;" class="me-0 mx-lg-5">
                        @include('components.section-loader')
                        <h1 class="h2">{{ __('Login') }}</h1>

                        {{-- <p class="fs-ms text-muted mb-2">
                            <a class="nav-link-style fs-ms" href="password-recovery.html">{{ __('Click here if you forgot password') }}</a>
                        </p> --}}

                        {{-- Flash Alert --}}
                        @include('components.alert')

                        <form id="login-form" class="needs-validation" method="POST" action="{{ route('login.post') }}" novalidate>
                            @csrf
                            <div class="input-group mt-3">
                                <i class="ai-mail position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                <input class="form-control rounded @error('email') is-invalid @enderror" type="email" value="{{ old('email') }}" name="email" placeholder="{{ __('Email Address') }}">
                            </div>
                            @error('email')
                                <div class="invalid-feedback" style="display: block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                            <div class="input-group mt-3">
                                <i class="ai-lock position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                <div class="password-toggle w-100">
                                    <input class="form-control @error('password') is-invalid @enderror" id="password_toggle" type="password" name="password" placeholder="{{ __('Password') }}">
                                    <label class="password-toggle-btn" aria-label="Show/hide password">
                                        <input class="password-toggle-check" type="checkbox">
                                        <span class="password-toggle-indicator"></span>
                                    </label>
                                </div>
                            </div>
                            @error('password')
                                <div class="invalid-feedback" style="display: block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                            <div class="d-flex justify-content-between align-items-center mt-3 mb-3 pb-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember_me" value="remember_me" name="remember_me">
                                    <label class="form-check-label" for="keep-signed-2">{{ __('Keep signed in') }}</label>
                                </div>
                            </div>

                            <button class="btn btn-primary d-block w-100" type="submit">{{ __('Login') }}</button>
                            <p class="fs-sm pt-3 mb-0">{{ __('Don’t have an account?') }}
                                <a href='{{ route('registration.email') }}' class='fw-medium' data-view='#signup-view'>{{ __('Sign Up') }}</a>
                            </p>
                        </form>
                    </div>
                    {{-- <div class="border-md-left border-md-top-0 border-top  border-sm-left-0 text-center mt-4 pt-4" style="flex: 1;">
                        <p class="fs-sm fw-medium text-heading">各種サービスでログイン</p><a class="btn-social bs-google bs-outline bs-lg mx-1 mb-2" href="#"><i class="ai-google"></i></a>
                    </div> --}}
                </div>
                {{-- <div class="text-center mt-3 mt-md-6">
                    <a href="#" class="fw-medium">{{ __('Click here if you forgot password') }}</a>
                </div> --}}
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
    <script src="{{ mix('/js/dist/login/index.js') }}" defer></script>
@endpush
