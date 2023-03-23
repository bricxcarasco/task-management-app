@extends('layouts.main')

@section('content')

<div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            <div class="col-12 col-md-9 offset-md-3">
                <div class="d-flex flex-column h-100 rounded-3 ">
                    <div class="position-relative">
                        <div class="tab-content mt-2">
                            <div class="tab-pane fade active show">
                                <div class="position-relative">
                                    <h3 class="py-3 mb-0 text-center">
                                        {{ __('User document header', [
                                            'fullname' => $service->data->full_name ?? $service->data->organization_name,
                                        ]) }}
                                    </h3>
                                </div>

                                <div class="text-center position-relative p-2  mb-2">
                                    @if(is_null($folderDetails->directory_id))
                                        <a href="{{ route('document.default-list') }}">
                                            <i class="ai-arrow-left message__back"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('document.folder-file-list', $folderDetails->directory_id) }}">
                                            <i class="ai-arrow-left message__back"></i>
                                        </a>
                                    @endif
                                    <strong>
                                        {{ $folderDetails->document_name }}
                                    </strong>
                                </div>

                                <form id='form' action="{{ route('document.folder-file-list', $directoryId) }}" method="get">
                                    <div class="input-group">
                                        <input class="form-control text-center @error('search') is-invalid @enderror" type="text" name='search' id='search' placeholder="{{ __('Find Documents') }}" value="{{ old('search') }}">
                                        <button class="btn btn-translucent-dark" type="button" id='searchBTN'>{{ __('Search') }}</button>
                                        @error('search')
                                            <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                            </div>
                                        @enderror
                                    </div>
                                </form>

                                {{-- Default Section - Folder/File List --}}
                                <default-section
                                    :service='@json($service)'
                                    :request_data='@json($requestData)'
                                    :directory_id='{{ $directoryId }}'>
                                ></default-section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
@endpush

@push('js')
<script type="text/javascript">
    $(document).ready(function() {
        var search = "{{ $requestData['search'] ?? '' }}";

        if(search !== ''){
            $('#search').val(search);
        }

        // Submit on selected change
        $('#searchBTN').click(function() {
            $('#form').submit();
        });
    });
</script>
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
    <script src="{{ mix('js/dist/document-management/index.js') }}" defer></script>
@endpush