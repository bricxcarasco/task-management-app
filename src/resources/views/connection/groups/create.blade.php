@extends('layouts.main')

@section('content')
    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            <div class="col-12 offset-md-3 col-md-9">
                <div class="d-flex flex-column h-100 rounded-3 ">
                    <div class="position-relative">
                        {{-- Connection name & tabs section --}}
                        @include('connection.components.name-tabs-section', [
                        'fullname' => $rio->full_name,
                        'active_tab' => 'group'
                        ])

                        <div>
                            <div class="position-relative mb-4">
                                <a href="{{ route('connection.groups.index') }}" class="btn btn-secondary">
                                    <i class="ai-arrow-left"></i>
                                    {{ __('Return to the connection group list screen') }}
                                </a>
                            </div>

                            {{-- Create group form --}}
                            <form method="POST" action="{{ route('connection.groups.store') }}">
                                @csrf
                                <div class="mt-2">
                                    <p>{{ __('Create a new connection group') }}</p>
                                    <div class="col-sm-12 mb-3">
                                        <label class="form-label" for="reg-fn">
                                            {{ __('Connection group name') }} {{ __('(required)') }}
                                            <sup class="text-danger ms-1">*</sup>
                                        </label>
                                        <input class="form-control @error('group_name') is-invalid @enderror" type="text"
                                            name="group_name" value="{{ old('group_name', $defaultName) }}">
                                        @error('group_name')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button class="btn btn-primary">{{ __('Create') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/connection-group/index.js') }}" defer></script>
@endpush
