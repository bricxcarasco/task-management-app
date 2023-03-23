@php
    use App\Enums\YearsOfExperiences;
@endphp

@extends('layouts.main')

@section('content')
    <!-- Section loader-->
    @include('connections.components.sections.section-loader')

    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            <div class="col-12 offset-md-3 col-md-9">
                <div class="d-flex flex-column h-100 rounded-3 ">
                    <div class="position-relative">
                        <!-- Connection tabs-->
                        @include('connections.components.sections.connection-tabs')
                        <div>
                            <div class="position-relative mb-4">
                                <a href="{{ route('connection.search.search') }}" class="btn btn-secondary" id="backBTN" disabled>
                                    <i class="ai-arrow-left"></i>
                                    {{ __('Return to the search screen') }}
                                </a>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <p class="mb-0 pe-2">{{$searchResult->total()}} {{ __('Cases') }}</p>
                                <div class="text-end" style="flex: 1;">
                                    <form id='form' action="{{ route('connection.search.result') }}" method="get">
                                    {{ csrf_field() }}
                                        <div class="form-check">
                                            <input class="form-check-input float-none" type="checkbox" id="exclude">
                                            <label class="form-check-label" for="exclude">{{ __('Exclude connected RIO/NEOs') }}
                                            </label>
                                        </div>
                                        <div class="clearfix"></div>
                                    </form>
                                </div>
                            </div>
                            <div class="card p-4 shadow mt-2">
                                <div class="connection__wrapper">
                                    <ul class="connection__lists list-group list-group-flush mt-2">
                                        @foreach($searchResult as $results)
                                            <li class="list-group-item px-0 py-2 position-relative list--white px-2">
                                                <span class="fs-xs c-primary ms-2">{{ $results->name }}</span>
                                                <div class="vertical-right d-flex align-items-center justify-content-center">
                                                    @if($results->service === 'rio')
                                                        <a href="{{ route('rio.profile.introduction', $results->id) }}"><i class="ai-arrow-right"></i></a>
                                                    @elseif($results->service === 'neo')
                                                        <a href="{{ route('neo.profile.introduction', $results->id) }}"><i class="ai-arrow-right"></i></a>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    @if($searchResult->total() !== 0)
                                        {{ __('Displaying results', ['count' => $searchResult->count(), 'total' => $searchResult->total()]) }}
                                    @else
                                        {{ __('There are no results to display') }}
                                    @endif
                                </div>
                                <div class="d-flex justify-content-center mt-2">
                                    {!! $searchResult->links() !!}
                                </div>
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
    const data = {
        search_target: "{{ $requestData['search_target'] }}",
        years_of_experience: "{{ $requestData['years_of_experience'] ?? null }}",
        business_category: "{{ $requestData['business_category'] ?? null }}",
        free_word: "{{ $requestData['free_word'] ?? null }}",
        exclude_connected: "{{ $requestData['exclude_connected'] ?? 0 }}",
    };

    $(document).ready(function() {
        $('#loadingSection').hide();

        // Set checkbox status
        if(data['exclude_connected'] == 1){
            $('#exclude').prop('checked', true);
        }
        
        // Submit updated search filter on checkbox status change
        $('#exclude').change(function() {
            $('#loadingSection').show();
            data['exclude_connected'] = 0;
            if(this.checked) {
                data['exclude_connected'] = 1;
            }

            $('#form').append('<input type="hidden" name="search_target" value="'+data['search_target']+'" />');
            $('#form').append('<input type="hidden" name="years_of_experience"  value="'+data['years_of_experience']+'" />');
            $('#form').append('<input type="hidden" name="business_category"  value="'+data['business_category']+'" />');
            $('#form').append('<input type="hidden" name="free_word"  value="'+data['free_word']+'" />');
            $('#form').append('<input type="hidden" name="exclude_connected"  value="'+data['exclude_connected']+'" />');

            $('#form').submit();
        });

        // Save session on back
        $('#backBTN').click(function(event){
            event.preventDefault();
            $('#loadingSection').show();
            $.ajax({
                url: "/connection/search/results/store-search-filters-to-session",
                dataType: 'json',
                type:'GET',
                beforeSend: function (request) {
                        let csrfToken = $('meta[name="csrf-token"]').attr('content');
                        let lang = $('html').attr('lang');

                        request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        request.setRequestHeader('Accept-Language', lang);
                    },
                data,
                success: function(data) {
                    let url = "{{ route('connection.search.search') }}";
                    document.location.href = url;
                }
            });
        });
    });
</script>
@endpush