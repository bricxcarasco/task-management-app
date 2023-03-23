@extends('layouts.main')

@section('content')
    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        {{-- Alert --}}
        @include('components.alert')

        <div class="row">
            <div class="col-12 offset-md-3 col-md-9">
                <div class="d-flex align-items-center justify-content-center d-none d-md-flex mb-0 mb-md-2 position-relative">
                    <a href="{{ route('rio.profile.introduction') }}" class="btn btn-secondary btn--back">
                        <i class="ai-arrow-left"></i>
                    </a>
                    <h3>{{ __('Privacy Settings') }}</h3>
                </div>
                <div class="d-flex flex-column h-100 rounded-3 ">
                     <div class="border-bottom position-relative d-block d-md-none">
                        <a href="{{ route('rio.profile.introduction') }}" class="btn btn-secondary btn--back">
                            <i class="ai-arrow-left"></i>
                        </a>
                        <h3 class="p-3 mb-0 text-center">{{ __('Privacy Settings') }}</h3>
                    </div>
                    <p class="mt-4 fs-4">{{ __('Restrictions on accepting connection applications') }}</p>
                    <div class="d-flex flex-column  bg-light rounded-3 shadow-lg p-2">
                        <form method="POST" action="{{ route('rio.privacy.update') }}" novalidate>
                            @csrf

                            {{-- Personal restrictions --}}
                            <div class="py-2 p-md-3 mt-2">
                                <label class="form-label">{{ __('Personal restrictions') }}</label>
                                <div class="pt-3 qualification__wrapper">
                                    <select class="form-select @error('accept_connections') is-invalid @enderror"
                                        name="accept_connections">
                                        @foreach ($personalRestrictions as $value => $text)
                                            <option value={{ $value }}
                                                {{ old('accept_connections', $rioPrivacy->accept_connections) == $value ? 'selected' : '' }}>
                                                {{ $text }}</option>
                                        @endforeach
                                    </select>
                                    @error('accept_connections')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Restrictions by participating NEOs --}}
                            <div class="py-2 p-md-3 mt-2">
                                <label class="form-label">{{ __('Restrictions by participating NEOs') }}</label>
                                <div class="pt-3 qualification__wrapper">
                                    <select class="form-select @error('accept_connections_by_neo') is-invalid @enderror"
                                        name="accept_connections_by_neo">
                                        @foreach ($participatingRestrictions as $value => $text)
                                            <option value={{ $value }}
                                                {{ old('accept_connections_by_neo', $rioPrivacy->accept_connections_by_neo) == $value ? 'selected' : '' }}>
                                                {{ $text }}</option>
                                        @endforeach
                                    </select>
                                    @error('accept_connections_by_neo')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit button --}}
                            <div class="col-md-6 offset-md-3 pt-2">
                                <button class="btn btn-primary d-block w-50 mx-auto" type="submit">{{ __('Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/rio/index.js') }}" defer></script>
@endpush
