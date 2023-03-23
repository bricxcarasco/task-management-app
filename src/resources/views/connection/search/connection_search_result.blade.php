@extends('layouts.main')

@section('content')
    <!-- Section loader-->
    @include('components.section-loader', ['show' => false])

    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            <div class="col-12 offset-md-3 col-md-9">
                <div class="d-flex flex-column h-100 rounded-3 ">
                    <div class="position-relative">
                        {{-- Connection name & tabs section --}}
                        @include('connection.components.name-tabs-section', [
                            'fullname' => $service->data->full_name ?? $service->data->organization_name,
                            'active_tab' => 'find'
                        ])

                        <div>
                            <div class="position-relative mb-4">
                                <a href="{{ route('connection.search.search') }}" class="btn btn-secondary" id="backBTN" disabled>
                                    <i class="ai-arrow-left"></i>
                                    {{ __('Return to the search screen') }}
                                </a>
                            </div>

                            @include('connection.search.connection_search_result_items')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script type="text/javascript">
    const data = {
        search_target: "{{ $requestData['search_target'] }}",
        years_of_experience: "{{ $requestData['years_of_experience'] ?? null }}",
        business_category: "{{ $requestData['business_category'] ?? null }}",
        free_word: "{{ $requestData['free_word'] ?? null }}",
        exclude_connected: "{{ $requestData['exclude_connected'] ?? 0 }}",
    };

    $(document).ready(function() {
        const loader = $('.js-section-loader');

        // Save session on back
        $('#backBTN').click(function(event){
            event.preventDefault();
            loader.removeClass('d-none');
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
                complete: function(result) {
                    let url = "{{ route('connection.search.search') }}";
                    document.location.href = url;
                }
            });
        });
    });
</script>
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/connections/index.js') }}" defer></script>
@endpush