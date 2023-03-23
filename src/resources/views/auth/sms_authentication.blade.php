@php
    use App\Enums\OTPTypes;
@endphp

@extends('layouts.guest')

@section('content')
<section class="container d-flex align-items-center pt-7 pb-3 pb-md-4 h-md-100" style="flex: 1 0 auto;">
    <div class="w-100 pt-3 pt-md-0 position-relative">
        {{-- Page Loader --}}
        @include('components.section-loader')

        <div class="row justify-content-center pt-4 pt-md-0">
            <div class="row justify-content-center pt-4">
                <div class="col-lg-7 col-md-9 col-sm-11">

                    <h1 class="h2 pb-3 text-center">{{ __('SMS Authentication') }}</h1>
                    <p class="fs-sm">{{ __('messages.sms_auth_code_description', ['phone' => $phoneNumber]) }}</p>

                    {{-- Flash Alert --}}
                    @include('components.alert')

                    <div class="bg-white rounded-3 px-3 py-5 mb-5">
                        <form method="POST" class="needs-validation p-2 js-registration-complete" action="{{ route('registration.complete') }}" autocomplete="off" novalidate>
                            @csrf
                            <input type="hidden" name="identifier" value={{ $identifier }}>
                            <input type="hidden" name="otp_type" value={{ $otpType }}>
                            <div class="mb-4 pb-1">
                                <div class="d-flex">
                                    <div class="row offset-md-2 col-md-8">
                                        <div class="col-3 px-2">
                                            <input name="code[]"
                                                type="text"
                                                class="js-code-field form-control form-control--plain text-center px-2"
                                                maxlength="1"
                                                required
                                            >
                                        </div>
                                        <div class="col-3 px-2">
                                            <input name="code[]"
                                                type="text"
                                                class="js-code-field form-control form-control--plain text-center px-2"
                                                maxlength="1"
                                                required
                                            >
                                        </div>
                                        <div class="col-3 px-2">
                                            <input name="code[]"
                                                type="text"
                                                class="js-code-field form-control form-control--plain text-center px-2"
                                                maxlength="1"
                                                required
                                            >
                                        </div>
                                        <div class="col-3 px-2">
                                            <input name="code[]"
                                                type="text"
                                                class="js-code-field form-control form-control--plain text-center px-2"
                                                maxlength="1"
                                                required
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="fs-sm text-center mb-4">{{ __('messages.sms_auth_code_input_description', ['phone' => $phoneNumber]) }}</p>
                            <button class="btn btn-primary d-block w-25 mx-auto" type="submit">{{ __('Authenticate') }}</button>
                        </form>

                        <hr class="my-5" />

                        {{-- Resend --}}
                        <form method="POST" class="needs-validation p-2 js-sms-resend" action="{{ route('registration.sms.send-otp') }}">
                            <div class="text-center">
                                @csrf
                                <input type="hidden" name="resend" value="1">
                                <input type="hidden" name="type" value="{{ OTPTypes::SMS }}">
                                <button class="btn btn-link fs-sm py-0" type="submit">{{ __('Resend verification code') }}</button>
                            </div>
                        </form>

                        {{-- Send Voice OTP --}}
                        <form method="POST" class="needs-validation p-2 js-voice-send" action="{{ route('registration.sms.send-otp') }}">
                            <div class="text-center">
                                @csrf
                                <input type="hidden" name="resend" value="1">
                                <input type="hidden" name="type" value="{{ OTPTypes::VOICE }}">
                                <button class="btn btn-link fs-sm py-0" type="submit">{{ __('Send Voice OTP') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            /**
             * Event listener for displaying page loader
             */
            $(document).on('submit', '.js-registration-complete, .js-sms-resend, .js-voice-send', function() {
                $('.js-section-loader').removeClass('d-none');
            })

            /**
             * Event listener for clearing code
             */
            $(document).on('keydown', '.js-code-field', function(event) {
                if (event.keyCode === 8) {
                    if ($(this).val() === '') {
                        $(this).parent()
                            .prev('div')
                            .children('.js-code-field')
                            .focus();
                    }
                }
            })

            /**
             * Event listener for entering code
             */
            $(document).on('keyup', '.js-code-field', function(event) {
                let value = $(this).val();
                let parsed = value.replace(/\D/g,'');
                $(this).val(parsed);

                if (parsed !== '') {
                    $(this).parent()
                        .next('div')
                        .children('.js-code-field')
                        .focus();
                }
            })
        });
    </script>
@endpush