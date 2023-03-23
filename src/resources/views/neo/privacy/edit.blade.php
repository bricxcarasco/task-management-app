@extends('layouts.main')

@section('content') 
<!-- Page content-->
<div class="container position-relative pb-4 mb-md-3 home pt-6 home--height">
    {{-- Alert --}}
    @include('components.alert')
    <div class="row">
        <div class="col-12 offset-md-3 col-md-9">
            <div class="d-flex flex-column h-100 rounded-3 ">
                <div class="border-bottom position-relative">
                    <a href="{{ route('neo.profile.introduction', $neo->id) }}" class="btn btn-secondary btn--back">
                        <i class="ai-arrow-left"></i>
                    </a>
                    <h3 class="p-3 mb-0 text-center">{{ __('Privacy Settings') }}</h3>
                </div>
                <form method="POST" action="{{ route('neo.privacy.update', $neo->id) }}" novalidate>
                    @csrf
                    <div class="py-2 p-md-3 mt-2">
                        {{ __('Restrictions on accepting connection applications') }}
                        <div class="pt-3 qualification__wrapper">
                            <select class="form-select @error('accept_connections') is-invalid @enderror"
                                name="accept_connections">
                                @foreach ($restrictConnections as $value => $text)
                                    <option value={{ $value }}
                                        {{ old('accept_connections', $neoPrivacy->accept_connections) == $value ? 'selected' : '' }}>
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
                    <div class="py-2 p-md-3 mt-2">
                    {{ __('Participation application acceptance restrictions') }}
                        <div class="pt-3 qualification__wrapper">
                            <select class="form-select @error('accept_belongs') is-invalid @enderror"
                                name="accept_belongs">
                                @foreach ($acceptParticipations as $value => $text)
                                    <option value={{ $value }}
                                        {{ old('accept_belongs', $neoPrivacy->accept_belongs) == $value ? 'selected' : '' }}>
                                        {{ $text }}</option>
                                @endforeach
                            </select>
                            @error('accept_belongs')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 offset-md-3 pt-2">
                        <button class="btn btn-primary d-block w-50 mx-auto" type="submit">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
