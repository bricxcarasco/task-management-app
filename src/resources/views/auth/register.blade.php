@php
    use App\Enums\AffiliateTypes;
    use App\Enums\Rio\GenderType;
@endphp

@extends('layouts.guest')

@section('content')
<section class="container d-flex align-items-center pt-7 pb-3 pb-md-4 h-md-100" style="flex: 1 0 auto;">
    <div class="w-100 pt-3 pt-md-0">
        <div class="row align-items-center pt-2">
            <div class="col-md-12">
                {{-- Flash Alert --}}
                @include('components.alert')

                <h2 class="h3 text-md-center">{{ __('Member Information Registration') }}</h2>
                <p class="text-end">{{ __('It is required field.') }}</p>
                <form class="row needs-validation bg-white rounded-3 px-3 py-4 p-sm-4" method="POST" action="{{ route('registration.confirm') }}" id="register-form" novalidate>
                    @csrf
                    <input type="hidden" name="rd_code" value="{{ $affiliateCode['moshimo'] }}">
                    <input type="hidden" name="a8" value="{{ $affiliateCode['a8'] }}">
                    <input type="hidden" name="affiliate">
                    <input type="hidden" name="registration_token" value="{{ $token }}">
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="name">{{ __('Name') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <div class="row">
                            <div class="col-sm-6 col-6">
                                <input class="form-control @error('family_name') is-invalid @enderror"
                                    value="{{ old('family_name') }}" name="family_name" type="text"
                                    id="family_name" placeholder="{{ __('Surname') }}">
                                @error('family_name')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="col-sm-6 col-6">
                                <input class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name') }}" name="first_name" type="text"
                                    id="first_name" placeholder="{{ __('Given Name') }}">
                                @error('first_name')
                                <div class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="name_kana">{{ __('Name Furigana') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <div class="row">
                            <div class="col-sm-6 6 col-6">
                                <input class="form-control @error('family_kana') is-invalid @enderror"
                                    value="{{ old('family_kana') }}" name="family_kana" type="text"
                                    id="family_kana" placeholder="{{ __('Surname') }}">
                                @error('family_kana')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <div class="col-sm-6 6 col-6">
                                <input class="form-control @error('first_kana') is-invalid @enderror"
                                    value="{{ old('first_kana') }}" name="first_kana" type="text"
                                    id="first_kana" placeholder="{{ __('Given Name') }}">
                                @error('first_kana')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="present_address_prefecture">{{ __('Prefecture of Residences') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <select class="form-select select-present-address-prefecture @error('present_address_prefecture') is-invalid @enderror"
                            id="present_address_prefecture" name="present_address_prefecture">
                            @foreach ($prefectures as $prefecture)
                            <option value={{ $prefecture }} {{ old('present_address_prefecture') == $prefecture ? "selected" :""}}>{{ prefecture_value($prefecture) }}</option>
                            @endforeach
                        </select>
                        @error('present_address_prefecture')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-3 present_address_nationality" id="register-present_address_nationality">
                        <label class="form-label" for="present_address_nationality">{{ __('Country') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <input class="form-control @error('present_address_nationality') is-invalid @enderror"
                            value="{{ old('present_address_nationality') }}"
                            name="present_address_nationality" type="text" id="present_address_nationality">
                        @error('present_address_nationality')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="email">{{ __('Log-in E-mail Address') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <input class="form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ $email }}" type="email" id="email" readonly>
                        @error('email')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="password">{{ __('Login Password') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <input class="form-control @error('password') is-invalid @enderror"
                            name="password" type="password" id="password">
                        @error('password')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="password_confirmation">{{ __('Re-enter password') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <input class="form-control @error('password_confirmation') is-invalid @enderror"
                            name="password_confirmation" type="password" id="password_confirmation">
                        @error('password_confirmation')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="birth_date">{{ __('Birthday') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <p class="form-label" for="birth_date">{{ __('Profile setting is private') }}</p>
                        <input
                            class="form-control date-picker @error('birth_date') is-invalid @enderror"
                            value="{{ old('birth_date') ? old('birth_date') : '1975-01-01' }}"
                            name="birth_date"
                            type="date"
                            id="birth_date"
                            placeholder="yyyy-mm-dd"
                            >
                        @error('birth_date')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="gender">{{ __('Gender') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <div class="pt-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" 
                                    value="{{ GenderType::MALE }}"
                                    type="radio"
                                    id="gender-male"
                                    name="gender"
                                    @if((int) old('gender') === GenderType::MALE) checked @endif>
                                <label class="form-check-label" for="gender-male">{{ __('Male') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                    value="{{ GenderType::FEMALE }}"
                                    type="radio"
                                    id="gender-female"
                                    name="gender"
                                    @if((int) old('gender') === GenderType::FEMALE) checked @endif>
                                <label class="form-check-label" for="gender-female">{{ __('Female') }}</label>
                            </div>
                        </div>
                        @error('gender')
                        <div class="text-danger fs-xs mt-2">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="tel">
                            {{ __('Cell phone number') }}
                            <sup class="text-danger ms-1">*</sup>
                        </label>
                        <p class="form-label" for="tel">{{ __('Profile setting is private') }}</p>
                        <p class="form-label" for="tel">{{ __('Please enter only half-width numbers.') }}</p>
                        <div>
                            <input class="form-control" value="{{ old('tel_country') }}" type="hidden" name="tel_country">
                            <input class="form-control @error('tel') is-invalid @enderror"
                                value="{{ old('tel') }}" type="text" id="tel" name="tel" placeholder="09012345678">
                        </div>
                        @error('tel')
                        <div class="invalid-feedback d-block">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="secret_question">{{ __('Secret Question') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <select class="form-select @error('secret_question') is-invalid @enderror"
                            name="secret_question" id="secret_question">
                            @foreach ($secret_questions as $question)
                            <option value={{ $question }}>{{ get_secret_question_by_id($question) }}</option>
                            @endforeach
                        </select>
                        @error('secret_question')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="secret_answer">{{ __('Answer to Secret Question') }}
                            <sup class="text-danger ms-1">*</sup></label>
                        <input class="form-control @error('secret_answer') is-invalid @enderror"
                            value="{{ old('secret_answer') }}" name="secret_answer" type="text"
                            id="secret_answer">
                        @error('secret_answer')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-6 offset-md-3 pt-2">
                        <button class="btn btn-primary d-block w-100" type="submit">{{ __('Confirmation') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('pre-css')
    <link rel="stylesheet" href="{{ asset('css/intlTelInput.min.css') }}">
@endpush

@push('js')
<script src="{{ asset('js/intl-tel-input/intlTelInput.min.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function(event) { 
        // Allow manual input of date
        const altFormat = "F j, Y";
        const datepicker = document.querySelector('.date-picker');
        /* eslint-disable no-undef */
        const datePicker = flatpickr(datepicker, {
            allowInput: true,
            locale: 'ja',
            disableMobile: 'true',
            altFormat,
            onClose: function(selectedDates, dateStr, instance){
                const newValue = datePicker._input.value;
                const isValid = Date.parse(newValue);
                if (!isNaN(isValid)) {
                    const parsedDate = new Date(newValue);
                    const formattedDate = datePicker.formatDate(parsedDate, 'Y-m-d');
                    if (formattedDate !== '0NaN-aN-aN') {
                        datePicker.setDate(formattedDate, true, 'Y-m-d');
                    }
                }
            },
        });

        datepicker.autocomplete="off";
        datePicker._input.addEventListener('keyup', (event) => {
            const value = datePicker._input.value;
            const isValid = Date.parse(value);
            if (!isNaN(isValid)) {
                const parsedDate =  new Date(value);
                const formattedDate = datePicker.formatDate(parsedDate, 'Y-m-d');

                if(value === formattedDate) {
                    datePicker.setDate(value, true, 'Y-m-d');
                }
            }
        });

        const telInput = document.querySelector("#tel");
        const telCountryInput = document.querySelector("[name=tel_country]");
        const telIntl = window.intlTelInput(telInput, {
            initialCountry: telCountryInput.value || 'jp',
            hiddenInput: 'tel',
            utilsScript: '{{ asset("js/intl-tel-input/utils.min.js") }}'
        });

        /**
         * Event listener for country input selection
         */
        telInput.addEventListener('countrychange', function() {
            const selectedCountry = telIntl.getSelectedCountryData();
            telCountryInput.value = selectedCountry.iso2 || 'jp';
        })

        /**
         * Handle form submission.
         */
        $('#register-form').on('submit', function() {
            if ($('input[name="rd_code"]').val()) {
                $('input[name="affiliate"]').val('{{ AffiliateTypes::MOSHIMO }}');
            }

            if ($('input[name="a8"]').val()) {
                $('input[name="affiliate"]').val('{{ AffiliateTypes::A8 }}');
            }

           return true;
        });
    });
  </script>
@endpush
