@php
use App\Enums\Rio\ConnectionStatusType;
use App\Enums\ServiceSelectionTypes;
@endphp

@extends('layouts.main')

@section('content')

@include('components.profile_reference_url_modal')

<div class="container position-relative pb-4 mb-md-3 home pt-6 home--height">
    <div class="row pt-2">
        <div class="col-12">
            {{-- Avatar Card --}}
                <div class="card p-4">
                    @include('rio.profile.components.sections.profile-avatar')
                    @can('connect', $rio)
                        @if($service->type === ServiceSelectionTypes::RIO)
                            @if (!in_array($status['status'] ?? null, [ConnectionStatusType::HIDDEN, ConnectionStatusType::NOT_ALLOWED]))
                                <rio-connections :rio='@json($rio)' :service='@json($service)' :status='@json($status)' :neo_status='@json($neoStatus)'/>
                            @endif
                        @else
                            @if (!in_array($neoPrivacyStatus, [ConnectionStatusType::HIDDEN, ConnectionStatusType::NOT_ALLOWED]))
                                <rio-connections :rio='@json($rio)' :service='@json($service)' :status='@json($status)' :neo_status='@json($neoPrivacyStatus)'/>
                            @endif
                        @endif
                    @endcan  
                </div>
                <div class="bg-secondary p-4  mt-4 ">
                    {{-- Profile Navigation --}}
                    @include('rio.profile.components.sections.profile-tabs', $rio)

                    <!-- Self-Introduction -->
                    <div class="card p-4 shadow">
                        <p class="m-0">{!! nl2br(e($rio->rio_profile->self_introduce)) ?? '' !!}</p>
                    </div>

                    {{-- Profile Video --}}
                    @if(!empty($rio->rio_profile->profile_video))
                        <h3 class="mt-4">{{ __('Profile Video') }}</h3>
                        <div class="w-100 py-5 d-flex align-items-center justify-content-center">
                            <p>{{ $rio->rio_profile->profile_video }}</p>
                        </div>
                    @endif

                    {{-- Registered Products --}}
                    @if ($rio->products->isNotEmpty())
                        <h3 class="mt-4">{{ __('Registered Products') }}</h3>
                        <div class="row">
                            @foreach ($rio->products as $product)
                                @include('rio.profile.components.sections.registered-product')
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Settings Menu --}}
    @include('rio.profile.components.modals.management-menu-modal')
@endsection

@push('js')
<script>
    $(function () {
        $(document).on('click', '.openURLModal', function(event){
            const referenceUrl = $(this).attr('data-reference-url');
            
            $('#productReferenceURLModal').modal('show');
            $('#productReferenceURLModal input[type="hidden"]').val(referenceUrl);
        });

        $(document).on('click', '#productReferenceURLModal .redirectReferenceURL', function(event){
            const referenceUrl = $('#productReferenceURLModal input[type="hidden"]').val();
            
            window.open(referenceUrl, '_blank');

            $('#productReferenceURLModal').modal('hide');
        });
    });
</script>
@endpush

@push('vuejs')
    <script src="{{ mix('js/dist/rio/index.js') }}" defer></script>
@endpush
