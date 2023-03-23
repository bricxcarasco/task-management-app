@extends('layouts.main')

@section('content')
<div class="container pt-6 py-md-6">
    <div class="row align-items-center pt-2">
        <div class="col-md-12 col-md-9 offset-md-3">
            <h2 class="h3">{{ __('New NEO') }}</h2>
            <form class="row" method="POST" action="{{ route('neo.registration.complete', $group ? $group->id : null) }}">
                @csrf
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Organization name') }}
                        </label>
                    <div class="row">
                        <div class="col-sm-12">
                            <p>{{ $neo['organization_name'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Organization name furigana') }}    
                    </label>
                    <div class="row">
                        <div class="col-sm-12">
                            <p>{{ $neo['organization_kana'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Organizational attributes') }}
                    </label>
                    <p>{{ organization_type_value($neo['organization_type']) }}</p>
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Date of Establishment') }}
                    </label>
                    <p>{{ japanese_date_format($neo['establishment_date']) }}</p>
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Location: Prefecture') }}
                    </label>
                    <p>{{ prefecture_value($neoProfile['prefecture']) }}</p>
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Location: Municipality') }}
                    </label>
                    <p>{{ $neoProfile['city'] }}</p>
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Location: Address') }}
                    </label>
                    <p>{{ $neoProfile['address'] }}</p>
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Location: Building/Apartment') }}
                    </label>
                    <p>{{ $neoProfile['building'] }}</p>
                </div>
                @if(!is_null($neoExpertEmail['email']) && !empty($neoExpertEmail['email']))
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="reg-fn">
                            {{ __('Email Address') }}
                        </label>
                        <p>{{ $neoExpertEmail['email'] }}</p>
                    </div>
                @endif
                @if(!is_null($neo['tel']) && !empty($neo['tel']))
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="reg-fn">
                            {{ __('Telephone No.') }}
                        </label>
                        <p>{{ $neo['tel'] }}</p>
                    </div>
                @endif
                @if(!is_null($neoExpertUrl['site_url']) && !empty($neoExpertUrl['site_url']))
                    <div class="col-sm-12 mb-3">
                        <label class="form-label" for="reg-fn">
                            {{ __('URL') }}
                        </label>
                        <p>{{ $neoExpertUrl['site_url'] }}</p>
                    </div>
                @endif
                <div class="col-sm-12 mb-3">
                    <p>
                        ■{{ __('Security settings') }}
                    </p>
                    <label class="form-label" for="reg-fn">
                        {{ __('Connection application reception setting') }}
                    </label>
                    <p>{{ neo_accept_connection_value($neoPrivacy['accept_connections']) }}</p>
                </div>
                <div class="col-sm-12 mb-3">
                    <label class="form-label" for="reg-fn">
                        {{ __('Participation application reception setting') }}
                    </label>
                    <p>{{ neo_accept_belong_value($neoPrivacy['accept_belongs']) }}</p>
                </div>
                <div class="col-sm-12 mb-3">
                    <p>
                        ■{{ __('Self-introduction') }}
                    </p>
                    <p>{{ $neoProfile['self_introduce'] ?? '' }}</p>
                </div>
                <div class="col-sm-12 mb-3">
                    <p>
                        {{ __('Overseas Support') }}
                    </p>
                    <p>{{ overseas_value($neoProfile['overseas_support']) }}</p>
                </div>
                <div class="col-md-6 pt-2">
                    <button class="btn btn-primary d-block w-100" type="submit">{{ __('Register') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/neo/index.js') }}" defer></script>
@endpush