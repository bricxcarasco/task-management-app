
@extends('layouts.main')

@section('content')
<div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
    {{-- Flash Alert --}}
    @include('components.alert')

    <div class="row">
        <div class="col-12 col-md-9 offset-md-3">
            <h3 class="py-3 mb-0 text-center">
              {{ __('Service owned knowledge', ['name' => $serviceName]) }}
            </h3>
            <a href="{{ route('knowledges.index') }}" class="btn btn-link">
                <i class="ai-arrow-left">{{ __('Back to index') }}</i>
            </a>
            <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
                <div class="position-relative">
                    <h3 class="py-3 mb-0 text-center">{{ __('Draft list') }}</h3>
                </div>
                <div class="tab-content mt-2">
                    <div class="tab-pane fade active show">
                        <p class="bg-dark c-white p-2 mb-0">{{ __('Draft article') }}</p>
                        <ul class="list-group list-group-flush">
                            @foreach ($articles as $article)
                                <li class="list-group-item px-0 py-2 position-relative list--white px-2">
                                    <div class="document-management-file-name">
                                        <div
                                            class="js-edit-draft"
                                            data-action="{{ route('knowledges.articles.create-article', $article->id) }}"
                                        >
                                            <i class="h2 m-0 ai-file-text"></i>
                                            <span class="fs-xs c-primary ms-2">{{ $article->task_title }}</span>
                                        </div>
                                    </div>
                                    <div class="vertical-right hasDropdown dropstart">
                                        <button class="js-delete-group-btn btn btn-link"
                                            data-action="{{ route('knowledges.articles.drafts.delete', ['knowledge' => $article->id]) }}">
                                            <i class="color-primary ai-trash-2"></i>
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                            @if ($articles->isEmpty())
                                <div class="d-flex justify-content-center mt-3 pb-3">
                                    {{ __('There are no articles') }}
                                </div>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('knowledges.components.delete-confirmation-modal')

@endsection

@push('js')
    <script>
        $(function() {
            deleteArticleDraftHandler();
            editArticleDraftHandler();
        });

        /**
         * Article draft delete modal handler
         */
        function deleteArticleDraftHandler() {
            $('.js-delete-group-btn').on('click', function() {
                let formSelector = $('#delete-confirm-form');

                // Set form action
                formSelector.attr('action', $(this).data('action'));

                // Show modal
                $('#delete-confirmation-modal').modal('show');
            });
        }

        /**
         * Article draft edit handler
         */
        function editArticleDraftHandler() {
            $('.js-edit-draft').on('click', function() {
                let url = $(this).data('action');
                window.location.href = url;
            });
        }
    </script>
@endpush

