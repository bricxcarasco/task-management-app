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
                        'active_tab' => 'request'
                        ])

                        <div class="tab-content">
                            @include('connection.lists.connection-requests-list-items')
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
        var mode = "{{ $mode }}";
        if(mode !== ''){
            $('#mode').val(mode);
        }

        // Submit on selected change
        $('#mode').change(function() {
            $('#form').submit();
        });
    });
</script>
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/connections/index.js') }}" defer></script>
@endpush
