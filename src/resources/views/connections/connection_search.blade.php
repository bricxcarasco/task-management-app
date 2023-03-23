@php
    use App\Enums\YearsOfExperiences;
    
    $search_target = session('connections.search.search_target') ?? 0;
    $years_of_experience = session('connections.search.years_of_experience');
    $business_category = session('connections.search.business_category');
    $free_word = session('connections.search.free_word');

    session()->forget('connections.search');
@endphp

@extends('layouts.main')

@section('content')
<div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
    <div class="row">
        <div class="col-12 offset-md-3 col-md-9">
            <div class="d-flex flex-column h-100 rounded-3 ">
                <div class="position-relative">
                    <!-- Connection tabs-->
                    @include('connections.components.sections.connection-tabs')
                    <div>
                        {{ __('Enter your search criteria') }}
                        <div class="card p-4 shadow mt-2">
                            <form action="{{ route('connection.search.result') }}" method="GET">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Search Target') }}</label>
                                    <div class="form-check form-check-inline @error('search_target') service-target-invalid is-invalid @enderror">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label" for="rio">RIO</label>
                                            <input class="form-check-input" type="radio" value=0 id="rio" name="search_target">
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label " for="neo">NEO</label>
                                            <input class="form-check-input" type="radio" value=1 id="neo" name="search_target">
                                        </div>
                                    </div>
                                    @error('search_target')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="select-input" class="form-label">{{ __('Industry') }}</label>
                                    <select class="form-select @error('business_category') is-invalid @enderror" id="business_category" name="business_category">
                                        <option value="">{{ __('Please select one') }}</option>
                                        @foreach ($businessCategories as $businessCategory => $businessCategoryName)
                                            <option value="{{ $businessCategory }}" {{ old('business_category') == $businessCategory ? "selected" :""}}>{{ $businessCategoryName }}</option>
                                        @endforeach
                                    </select>
                                    @error('business_category')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="select-input" class="form-label">{{ __('Years of Experience') }}</label>
                                    <select class="form-select @error('years_of_experience') is-invalid @enderror" id="years_of_experience" name="years_of_experience">
                                        <option value="">{{ __('Please select one') }}</option>
                                        @foreach ($yearsOfExperiences as $yearsOfExperience)
                                            <option value={{ $yearsOfExperience }} {{ old('years_of_experience') == $yearsOfExperience ? "selected" :""}}>{{ YearsOfExperiences::getDescription($yearsOfExperience) }}</option>
                                        @endforeach
                                    </select>
                                    @error('years_of_experience')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="text-input" class="form-label">{{ __('Free word') }}</label>
                                    <input class="form-control @error('free_word') is-invalid @enderror" type="text" id="free_word" name="free_word" value="{{ old('free_word') }}">
                                    @error('free_word')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-center align-items-center mt-3">
                                    <div class="btn btn-success me-1" id="loadingBTN">
                                        <div class="spinner-border spinner-border-sm " role="status"></div>
                                    </div>
                                    <input class="btn btn-success me-1" type="submit" value="{{ __('Search') }}" id="searchBTN">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#loadingBTN').hide();
            var search_target = "{{ $search_target }}";
            var years_of_experience = "{{ $years_of_experience }}";
            var business_category = "{{ $business_category }}";
            var free_word = "{{ $free_word }}";

            if(search_target !== ''){
                $(`input[name="search_target"][value=${search_target}]`).prop("checked", true);
            }

            if(years_of_experience !== ''){
                $('#years_of_experience').val(years_of_experience);
            }

            if(business_category !== ''){
                $('#business_category').val(business_category);
            }

            if(free_word !== ''){
                $('#free_word').val(free_word);
            }

            $('[method="GET"]').submit(function() {
                $("#loadingBTN").show();
                $("#searchBTN").hide();
                return true;
            });
        });
    </script>
@endpush

@push('css')
    <style scoped>
        .service-target-invalid {
            margin-left: .500rem;
            padding: .500rem 1rem .500rem 1rem;
            border: 1px solid #f74f78;
            border-radius: 0.75rem;
        }
    </style>
@endpush