@extends('layouts.guest')

@section('content')
<section class="container d-flex align-items-center pt-7 pb-3 pb-md-4 h-md-100" style="flex: 1 0 auto;">
    <div class="w-100 pt-3 pt-md-0 position-relative">
        {{-- Page Loader --}}
        @include('components.section-loader')

        <div class="row align-items-center pt-2">
            <div class="col-md-12">
                <h2 class="h3 text-md-center">{{ __('Member Information Registration') }}</h2>
                <div class="bg-white rounded-3 px-3 py-4 p-sm-4">
                    <form class="row" method="POST" action="{{ route('registration.sms.send-otp') }}">
                        @csrf
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-fn">{{ __('Name') }}<sup class="text-danger ms-1">*</sup></label>
                            <div class="row">
                                <div class="col-sm-12">
                                    <p>{{ $input_rio['family_name'] }} {{ $input_rio['first_name'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-fn">{{ __('Name Furigana') }}<sup class="text-danger ms-1">*</sup></label>
                            <div class="row">
                                <div class="col-sm-12">
                                    <p>{{ $input_rio['family_kana'] }} {{ $input_rio['first_kana'] }} </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-email">{{ __('Log-in E-mail Address') }}
                                <sup class="text-danger ms-1">*</sup></label>
                            <p>{{ $input_user['email'] }}</p>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-password">{{ __('Login Password') }}
                                <sup class="text-danger ms-1">*</sup></label>
                            <p>********</p>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-password">{{ __('Birthday') }}
                                <sup class="text-danger ms-1">*</sup></label>
                            <p>{{ japanese_date_format($input_rio['birth_date']) }}</p>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-password">{{ __('Gender') }}
                                <sup class="text-danger ms-1">*</sup></label>
                            <p>{{ gender_value($input_rio['gender']) }}</p>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-password">{{ __('Telephone No.') }}
                                <sup class="text-danger ms-1">*</sup></label>
                            <p>{{ $input_rio['tel'] }}</p>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-password">{{ __('Secret Question') }}
                                <sup class="text-danger ms-1">*</sup></label>
                            <p>{{ get_secret_question_by_id($input_user['secret_question']) }}</p>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="reg-password">{{ __('Answer to Secret Question') }}
                                <sup class="text-danger ms-1">*</sup></label>
                            <p>{{ $input_user['secret_answer'] }}</p>
                        </div>
                        <div class="col-sm-12 mb-4 text-center">
                            <input class="js-policy-checkbox form-check-input" type="checkbox" id="privacy-checkbox" name="privacy-checkbox" />
                            <label for="privacy-checkbox" class="fs-xs c-primary ms-2">
                                {{ __('I agree to the privacy policy') }}
                            </label>
                        </div>
                        <div class="col-md-6 offset-md-3 pt-2">
                            <button class="btn btn-primary d-block w-100" type="submit">{{ __('Register') }}</button>
                        </div>
                    </form>
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
            let buttonSelector = $("button[type=submit]");
            let checkboxSelector = '#privacy-checkbox';

            // Disable button by default
            buttonSelector.prop('disabled', true);

            // Toggle button based on privacy policy checkbox
            $(document).on('change', checkboxSelector, function() {
                if ($(checkboxSelector).prop('checked') === true) {
                    buttonSelector.prop('disabled', false);
                } else {
                    buttonSelector.prop('disabled', true);
                }
            });

            $(document).on('submit', 'form', function() {
                $('.js-section-loader').removeClass('d-none');
            })
        });
    </script>
@endpush
