@extends('layouts.main')

@section('content')
<div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 home--height">
    <div class="row">
    <!-- Content-->
    <div class="col-12 col-md-9 offset-md-3">
        <div class="d-flex align-items-center justify-content-center  mb-0 mb-md-2 position-relative border-bottom">
        <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn--back">
            <i class="ai-arrow-left"></i>
        </a>
        <h3>{{ __('Export') }}</h3>
        </div>
        <div class="mb-3">
            <label for="normal-input" class="form-label">■{{ __('Target period') }}</label>
            <form method="POST" action="{{ route('schedules.export-schedule') }}" class="d-inline" novalidate>
                @csrf
                <div class="d-flex align-items-center justify-content-between  mb-3">
                    <div class="col-5">
                        <input class="form-control date-picker rounded flex-1 @error('start_date') is-invalid @enderror" name="start_date" type="text" value="{{ $startDate }}" placeholder="{{ $startDate }}" >
                        @error('start_date')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                    <div class="col-2 text-center">
                        <span>-</span>
                    </div>
                    <div class="col-5">
                        <input class="form-control date-picker rounded flex-1 @error('end_date') is-invalid @enderror" name="end_date" type="text" value="{{ $endDate }}" placeholder="{{ $endDate }}" >
                        @error('end_date')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="normal-input" class="form-label">■{{ __('Target calendar') }}</label>
                    <select class="form-select" name="issuer">
                        @foreach($serviceSelections as $key => $service)
                            <option value="{{ $key }}">{{ $service }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" id="export-btn" class="btn btn-primary">{{ __('Create a CSV file') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

