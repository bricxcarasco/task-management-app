<div class="modal fade" id="documentAccessService" tabindex="-1" style="display: none;" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <input type="hidden" id="document_id" name="document_id" value="{{ $document->id ?? null }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            <div class="modal-header">
                <button class="btn-close" type="button" aria-label="Close" id="documentAccessServiceBTN"></button>
            </div>
            <div class="modal-body">
                @if($isRioAllowed || !empty($neosFiltered))
                    <div class="row">
                        <div class="col-12 text-center">
                                {{ __('This page can be accessed by the following entities') }} {{ __('Please select the entity you wish to access') }}
                        </div>
                    </div>
                    <table class="table">
                        <tbody>
                            @if($isRioAllowed)
                                <tr 
                                    data-service='RIO'
                                    data-id='{{ $currentUser->id }}'
                                >
                                    <td>
                                        <div class="col-12 text-center">
                                            {{ $currentUser->family_name.' '.$currentUser->first_name }} (RIO)
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if(!empty($neosFiltered))
                                @foreach ($neosFiltered as $neoFiltered)
                                    <tr 
                                        data-service='NEO'
                                        data-id='{{ $neoFiltered->id }}'
                                    >
                                        <td>
                                            <div class="col-12 text-center">
                                                {{ $neoFiltered->organization_name }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                @else
                    <div class="row">
                        <div class="col-12 text-center">
                            {{ __('Entities tied to this account are not authorized to access this page') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
