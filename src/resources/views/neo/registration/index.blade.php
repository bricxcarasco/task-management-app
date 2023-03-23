@php
    use App\Enums\Neo\OverseasSupportType;
@endphp

@extends('layouts.main')

@section('content')
<div class="container pt-6 py-md-6 home--height">

    {{-- Flash Alert --}}
    @include('components.alert')

    <div class="row align-items-center pt-2">
        <div class="col-12 col-md-9 offset-md-3">
            <h2 class="h3">{{ __('New NEO') }}</h2>
            <form class="row" method="POST" action="{{ route('neo.registration.confirm', $group ? $group->id : null) }}" novalidate>
                @csrf
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Organization name') }}
                        <sup class="text-danger ms-1">*</sup>
                    </label>
                    <input value="{{ $group ? $group->group_name : old('organization_name') }}" class="form-control @error('organization_name') is-invalid @enderror" name="organization_name" type="text" id="reg-fn">
                    @error('organization_name')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Organization name furigana') }}
                        <sup class="text-danger ms-1">*</sup>
                    </label>
                    <input value="{{ old('organization_kana') }}" class="form-control @error('organization_kana') is-invalid @enderror" name="organization_kana" type="text" id="reg-fn">
                    @error('organization_kana')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-email">
                        {{ __('Organizational attributes') }}
                        <sup class="text-danger ms-1">*</sup>
                    </label>
                    <select class="form-select @error('organization_type') is-invalid @enderror" name="organization_type" id="select-input">
                        @foreach ($organizationAttributes as $attribute)
                        <option value={{ $attribute }} {{ old('organization_type') == $attribute ? "selected" :""}}>{{ organization_type_value($attribute) }}</option>
                        @endforeach
                    </select>
                    @error('organization_type')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-email">
                        {{ __('Date of Establishment') }}
                        <sup class="text-danger ms-1">*</sup>
                    </label>
                    <input
                        class="form-control date-picker @error('establishment_date') is-invalid @enderror"
                        value="{{ old('establishment_date') }}"
                        name="establishment_date"
                        type="date"
                        id="establishment_date"
                        placeholder="yyyy-mm-dd"
                    >
                    @error('establishment_date')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 col-6 mb-3">
                    <label class="form-label" for="reg-email">
                        {{ __('Location: Prefecture') }}
                        <sup class="text-danger ms-1">*</sup>
                    </label>
                    <select class="form-select select-present-address-prefecture @error('prefecture') is-invalid @enderror" name="prefecture" id="select-input">
                        @foreach ($prefectures as $prefecture)
                        <option value={{ $prefecture }} {{ old('prefecture') == $prefecture ? "selected" :""}}>{{ prefecture_value($prefecture) }}</option>
                        @endforeach
                    </select>
                    @error('prefecture')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3" id="register-present_address_nationality">
                    <label class="form-label" for="reg-fn">
                        {{ __('Country') }}
                    </label>
                    <div class="row">
                        <div class="col-6 col-md-12">
                            <input value="{{ old('nationality') }}" class="form-control present_address_nationality @error('nationality') is-invalid @enderror" name="nationality" type="text" id="present_address_nationality">
                            @error('nationality')
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Location: Municipality') }}
                    </label>
                    <input value="{{ old('city') }}" class="form-control @error('city') is-invalid @enderror" name="city" type="text" id="reg-ln">
                    @error('city')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Location: Address') }}
                    </label>
                    <input value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" name="address" type="text" id="reg-ln">
                    @error('address')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Location: Building/Apartment') }}
                    </label>
                    <input value="{{ old('building') }}" class="form-control @error('building') is-invalid @enderror" name="building" type="text" id="reg-ln">
                    @error('building')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Email Address') }}
                    </label>
                    <input value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" name="email" type="text" id="reg-ln">
                    @error('email')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Telephone No.') }}
                    </label>
                    <input value="{{ old('tel') }}" class="form-control @error('tel') is-invalid @enderror" name="tel" type="number" id="reg-ln">
                    @error('tel')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('URL') }}
                    </label>
                    <input value="{{ old('site_url') }}" class="form-control @error('site_url') is-invalid @enderror" name="site_url" type="text" id="reg-ln">
                    @error('site_url')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <p>
                        ■{{ __('Security settings') }}
                    </p>
                    <label class="form-label" for="reg-email">
                        {{ __('Connection application reception setting') }}
                        <sup class="text-danger ms-1">*</sup>
                    </label>
                    <select class="form-select @error('accept_connections') is-invalid @enderror" name="accept_connections" id="select-input">
                        @foreach ($acceptConnections as $connection)
                        <option value={{ $connection }} {{ old('accept_connections') == $connection ? "selected" :""}}>{{ neo_accept_connection_value($connection) }}</option>
                        @endforeach
                    </select>
                    @error('accept_connections')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-email">
                        {{ __('Participation application reception setting') }}
                        <sup class="text-danger ms-1">*</sup>
                    </label>
                    <select class="form-select @error('accept_belongs') is-invalid @enderror" name="accept_belongs" id="select-input">
                        @foreach ($acceptBelongs as $belong)
                        <option value={{ $belong }} {{ old('accept_belongs') == $belong ? "selected" :""}}>{{ neo_accept_belong_value($belong) }}</option>
                        @endforeach
                    </select>
                    @error('accept_belongs')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <p>
                        ■{{ __('Self-introduction') }}
                    </p>
                    <textarea class="form-control @error('self_introduce') is-invalid @enderror" name="self_introduce" id="textarea-neo_self_introduce" rows="5" maxlength="500">{{ old('self_introduce') }}</textarea>
                    <div class="float-right" style="float: right;">
                        <span id="count-neo_self_introduce"></span> / 500
                    </div>
                    @error('self_introduce')
                    <div class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="overseas_support">{{ __('Overseas Support') }}
                        <sup class="text-danger ms-1">*</sup></label>
                    <div class="pt-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input"
                                value="{{ OverseasSupportType::NO }}"
                                type="radio"
                                id="overseas-no"
                                name="overseas_support"
                                @if($errors->any() && (int) old('overseas_support') === OverseasSupportType::NO) checked @endif
                            >
                            <label class="form-check-label" for="overseas-no">{{ __('No') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input"
                                value="{{ OverseasSupportType::YES }}"
                                type="radio"
                                id="overseas-yes"
                                name="overseas_support"
                                @if(!$errors->any() && (int) old('overseas_support') === OverseasSupportType::NO || (int) old('overseas_support') === OverseasSupportType::YES) checked @endif>
                            <label class="form-check-label" for="overseas-yes">{{ __('Yes') }}</label>
                        </div>
                    </div>
                    @error('overseas_support')
                    <div class="text-danger fs-xs mt-2">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                @if(false)
                    <div class="col-sm-12 mb-3">
                        <p>
                            {{ __('Profile Video') }}
                        </p>
                        <div class="d-flex align-items-center justify-content-between btn__group--alt">
                            <input class="form-control" type="file" id="file-input">
                            <button type="button" class="delete--file-input btn btn-secondary">
                                <i class="text-nav ai-x"></i>
                            </button>
                        </div>
                    </div>
                @endif
                <div class="col-md-6 offset-md-3 pt-2">
                    <button class="btn btn-primary d-block w-100" type="submit">
                        {{ __('Register') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/neo/index.js') }}" defer></script>
@endpush