@extends('layouts.guest')

@section('content')
<section class="container d-flex align-items-center pt-7 pb-3 pb-md-4 h-md-100" style="flex: 1 0 auto;">
    <div class="w-100 pt-3 pt-md-0">
        <div class="row justify-content-center pt-4 pt-md-0">
            <div class="row justify-content-center pt-4">
                <div class="col-lg-7 col-md-9 col-sm-11">
                    
                    <h1 class="h2 pb-3 text-md-center">{{ __('Header Member Registration') }}</h1>
                    <p class="fs-sm">{{ __('Paragraph Member Registration') }}</p>

                    {{-- Flash Alert --}}
                    @include('components.alert')

                    <div class="bg-white rounded-3 px-3 py-4 p-sm-4">
                        <form method="POST" class="needs-validation p-2" action="{{ route('registration.email.post', $referralCode) }}">
                            @csrf
                            <input type="hidden" name="rd_code" value="{{ $affiliateCode['moshimo'] ?? null }}">
                            <input type="hidden" name="a8" value="{{ $affiliateCode['a8'] ?? null }}">
                            <div class="mb-3 pb-1">
                                <label class="form-label" for="recovery-email">{{ __('Email Address') }}</label>
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}">
                                @error('email')
                                <div class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                            </div>
                            <button class="btn btn-primary d-block w-25 mx-auto" type="submit">{{ __('Send Button') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
