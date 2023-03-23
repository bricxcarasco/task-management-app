@php
    use App\Enums\Connection\ListFilters;
@endphp

@extends('layouts.main')

@section('content')
    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            <div class="col-12 offset-md-3 col-md-9">
                <div class="d-flex flex-column h-100 rounded-3 ">
                    <div class="position-relative">
                        {{-- Connection name & tabs section --}}
                        @include('connection.components.name-tabs-section', [
                        'fullname' => $service->data->full_name ?? $service->data->organization_name,
                        'active_tab' => 'manage'
                        ])

                        {{-- Connection list tabs section --}}
                        @include('connection.components.connection-list-tabs-section', [
                        'active_tab' => 'list'
                        ])

                        <div class="tab-content">
                            <div class="tab-pane fade active show">
                                <form id='form' action="{{ route('connection.connection-list') }}" method="get">
                                    <div class="input-group">
                                        <input class="form-control text-center @error('search') is-invalid @enderror" type="text" name='search' id='search' placeholder="{{ __('Find Connections') }}" value="{{ old('search') }}">
                                        <button class="btn btn-translucent-dark" type="button" id='searchBTN'>{{ __('Search') }}</button>
                                        @error('search')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <p class="mb-0" style="flex: 1;">{{ $result->total() . ' ' .__('Cases') }}</p>
                                        <div class="form-check">
                                            <select class="form-select form-select-sm w-100" name='mode' id="mode" >
                                                @foreach (ListFilters::asSelectArray() as $value => $text)
                                                    <option value="{{ $value }}">{{ $text }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
    
                                @include('connection.lists.connection-list-items')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script type="text/javascript">
    $(document).ready(function() {
        var mode = "{{ $requestData['mode'] ?? ListFilters::SHOW_ALL }}";
        var search = "{{ $requestData['search'] ?? '' }}";
        var page = 1;

        if(mode !== ''){
            $('#mode').val(mode);
        }

        if(search !== ''){
            $('#search').val(search);
        }

        // Submit on selected change
        $('#mode').change(function() {
            $('#form').submit();
        });

        // Submit on selected change
        $('#searchBTN').click(function() {
            $('#form').submit();
        });

        function changePage(){
            const loader = $('.js-section-loader');
            // Start loader
            loader.removeClass('d-none');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var url = "{{ route('connection.connection-list-item') }}";
            $('.page-item').addClass('disabled');
            fetch(url, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken
                },
                method: 'PATCH',
                credentials: "same-origin",
                body: JSON.stringify({
                    page_no: page,
                    mode: mode,
                    search: search,
                })
            })
            .then(response => {
                if (response.ok) {
                    loader.addClass('d-none');
                    return response.text();
                }
            })
            .then(data => {
                if (data) {
                    $("#connection-list-items").html(data);
                }
            })
            .catch(function(error) {
                console.log(error);
                loader.addClass('d-none');
            })
        }

        //Render list on page ready
        changePage();

        //Update list on page change
        $(document).on('click', '.pagination a', function(event){
            page = $(this).attr('href').split('page=')[1];
            event.preventDefault();
            changePage();
        });
    });
</script>
@endpush

