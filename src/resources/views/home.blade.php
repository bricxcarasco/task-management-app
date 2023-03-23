@extends('layouts.main')

@section('content')
@php
    header("Expires: Thu, 19 Nov 1981 08:52:00 GMT"); //Date in the past
    header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0"); //HTTP/1.1
    header("Pragma: no-cache");

    use App\Enums\PaidPlan\PlanType;
    use App\Enums\PaidPlan\ServiceType;
    use App\Enums\ServiceSelectionTypes;

    // Set form route
    $accessibleRoutes = (array) $serviceSession->plan_subscriptions->routes;
    $formRoute = route('forms.quotations.index');
    foreach ($accessibleRoutes as $accessibleRoute) {
        switch ($accessibleRoute) {
            case 'forms.quotations.*':
                $formRoute = route('forms.quotations.index');
                break 2;
            case 'forms.purchase-orders.*':
                $formRoute = route('forms.purchase-orders.index');
                break 2;
            case 'forms.delivery-slips.*':
                $formRoute = route('forms.delivery-slips.index');
                break 2;
            case 'forms.invoices.*':
                $formRoute = route('forms.invoices.index');
                break 2;
            case 'forms.receipts.*':
                $formRoute = route('forms.receipts.index');
                break 2;
            default:
                break;
        }
    }
@endphp
<div class="container position-relative pb-4 mb-md-3 home pt-6 home--height">
    <div class="row">
        <div class="col-12 offset-md-3 col-md-9">
            <div class="d-flex flex-column  bg-light rounded-3 shadow-lg p-2 home__customCard mb-4">
                <div class="d-sm-flex align-items-center justify-content-between text-center text-sm-start">
                    <h1 class="h3 text-nowrap">{{ __('Notification list') }}</h1>
                </div>
                <div class="notification__announcement">
                    <div class="d-flex flex-column rounded-3 bg-light shadow-lg mb-0">
                        <div class="table-responsive">
                            <table class="table table-bordered m-0">
                                <td>
                                    <p class="c-primary">
                                        平素は当社サービスのアカウント開設して頂き誠にありがとうございます。<br>
                                        ① アカウント開設後の流れは <a class="js-external-link" href="https://hero.ne.jp/flow/" target="_blank" rel="noopener">こちら</a><br>
                                        ② 操作ガイドは <a class="js-external-link" href="https://hero.ne.jp/guide/" target="_blank" rel="noopener">こちら</a><br>
                                        ③ よくある質問は <a class="js-external-link" href="https://hero.ne.jp/faq/" target="_blank" rel="noopener">こちら</a><br>
                                        ④ 不具合の解消報告・今後のスケジュールは <a class="js-external-link" href="https://hero.ne.jp/news_categories/system/" target="_blank" rel="noopener">こちら</a><br>
                                        ⑤ キャンペーン情報は <a class="js-external-link" href="https://hero.ne.jp/news_categories/campaign/" target="_blank" rel="noopener">こちら</a><br>
                                        ⑥ 事務局へ連絡は <a class="js-external-link" href="https://hero.ne.jp/contact/" target="_blank" rel="noopener">こちら</a><br>
                                    </p>
                                </td>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- @if ($notifications->isNotEmpty())
                <div class="notification__announcement">
                    <div class="d-flex flex-column rounded-3 bg-light shadow-lg mb-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table--notification m-0">
                                @foreach ($notifications as $notification)
                                    <tr class="{{ $notification->is_notification_read ? '' : 'unread' }}">
                                        <td class="text-muted align-middle text-center fs-md">
                                            {{ $notification->notification_date }}
                                        </td>
                                        <td>
                                            <a href="{{ route('notifications.read', $notification->id) }}"
                                                class="c-primary">
                                                <span class="text-muted fs-xs d-block">
                                                    {{ $notification->notification_recipient }}
                                                </span>
                                                {{ $notification->notification_content }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                @else
                    <p class="text-center my-4">
                        {{ __('No notifications') }}
                    </p>
                @endif -->
            </div>
            <div class="col-12 home__customCard mb-4 d-block d-md-none ">
                <div class="d-flex flex-column h-100 rounded-3 shadow-lg p-2 bg-light">
                @if (session()->has('ServiceSelected') && $serviceSession->type === 'NEO')
                    <a href="{{ route('neo.profile.introduction', ['neo' => $serviceSession->data->id]) }}" id="current-service" class="btn btn-icon d-flex align-items-center justify-content-between">
                        <label id="service-name">{{ $serviceSession->data->organization_name }}</label>
                        <i class="ai-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('rio.profile.introduction') }}" id="current-service" class="btn btn-icon d-flex align-items-center justify-content-between">
                        <label id="service-name">{{ auth()->user()->rio->family_name.' '.auth()->user()->rio->first_name }}</label>
                        <i class="ai-arrow-right"></i>
                    </a>
                @endif
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end d-none d-md-flex mb-0 mb-md-2">
                <div class="mb-md-2">
                    <div class="d-flex align-items-center">
                        @csrf
                        <div class="col-6">
                            <a href="{{ route('neo.registration.index') }}" class="nav-link">
                                + {{ __('Make a new NEO') }}
                            </a>
                         </div>
                        <i class="h4 text-nav ai-users me-2 mb-0"></i>
                        <select class="form-select" id="select-service">
                            <option
                                value="RIO"
                                id="{{ $currentUser->id }}"
                                {{
                                    $serviceSession->type === ServiceSelectionTypes::RIO
                                    && $currentUser->id === $serviceSession->data->id
                                    ? 'selected'
                                    : ''
                                }}
                            >
                                {{ $currentUser->family_name . ' ' . $currentUser->first_name }} (RIO）
                            </option>
                            @foreach ($neos as $neo) 
                                <option
                                    value="NEO"
                                    id="{{ $neo->id }}"
                                    {{
                                        $serviceSession->type === ServiceSelectionTypes::NEO
                                        && $neo->id === $serviceSession->data->id
                                        ? 'selected'
                                        : ''
                                    }}
                                >
                                    {{ $neo->organization_name }}
                                </option>
                             @endforeach
                        </select>
                    </div>
                   
                </div>
            </div>
            <div class="col-12 d-none d-md-block home__customCard mb-4">
                <div class="rounded-3 bg-light shadow-lg p-3">
                    @if (
                        session()->has('ServiceSelected')
                        && $serviceSession->type === ServiceSelectionTypes::NEO
                    )
                        <a href="{{ route('neo.profile.introduction', ['neo' => $serviceSession->data->id]) }}" id="current-service-selected" class="btn btn-icon d-flex align-items-center justify-content-between">
                            <label id="service-selected-name">{{ $serviceSession->data->organization_name }}</label>
                            <i class="ai-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ route('rio.profile.introduction') }}" id="current-service-selected" class="btn btn-icon d-flex align-items-center justify-content-between">
                            <label id="service-selected-name">{{ auth()->user()->rio->family_name.' '.auth()->user()->rio->first_name }}</label>
                            <i class="ai-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-column  bg-light rounded-3 shadow-lg p-2">
                <div class="py-2 p-md-3">
                    <div class="d-sm-flex align-items-center justify-content-between pb-4 text-center text-sm-start">
                        <h1 class="h3 mb-2 text-nowrap">{{ __('Services') }}</h1>
                    </div>
                    <div class="page__home">
                        <a href="{{ route('connection.connection-list') }}" class="btn btn-secondary btn-icon page__homeIcon">
                            <i class="d-block ai-users"></i>
                            {{ __('Connection') }}
                        </a>
                        <a href="{{ route('connection.search.search') }}" class="btn btn-secondary btn-icon page__homeIcon">
                            <i class="d-block ai-user-check"></i>
                            {{ __('Expert search') }}
                        </a>
                        <!-- <a href="#" class="btn btn-secondary btn-icon page__homeIcon">
                            <i class="d-block ai-video"></i>
                            {{ __('Webinar') }}
                        </a> -->
                        @if (in_array('classifieds.*', $accessibleRoutes)) 
                            <a href="{{ route('classifieds.sales.index') }}" class="btn btn-secondary btn-icon page__homeIcon">
                                <i class="d-block ai-shopping-cart"></i>
                                {{ __('Buy/Sell') }}
                            </a>
                        @endif
                        <a href="{{ route('knowledges.index') }}" class="btn btn-secondary btn-icon page__homeIcon">
                            <i class="d-block ai-book"></i>
                            {{ __('Knowledge') }}
                        </a>
                        @if (in_array('document.*', $accessibleRoutes))
                            <a href="{{ route('document.default-list') }}" class="btn btn-secondary btn-icon page__homeIcon">
                                <i class="d-block ai-folder"></i>
                                {{ __('Document Management') }}
                            </a>
                        @endif
                        @if (
                            !$availableSlot['expired'] 
                            && !empty($availableSlot['slot']) 
                            || in_array(
                                ServiceType::ELECTRONIC_CONTRACT, 
                                (array) $serviceSession->plan_subscriptions->plan_options
                            )
                            || in_array(
                                'electronic-contracts.*', 
                                $accessibleRoutes
                            )
                        )
                            @if (
                                $serviceSession->type === ServiceSelectionTypes::RIO
                                ||
                                (
                                    $serviceSession->type === ServiceSelectionTypes::NEO
                                    && !$serviceSession->data->is_member
                                )
                            )
                                <a href="{{ route('electronic-contracts.index') }}" class="btn btn-secondary btn-icon page__homeIcon">
                                    <i class="d-block ai-archive"></i>
                                    {{ __('Electronic Contracts') }}
                                </a>
                            @endif
                        @endif
                        <!-- <a href="#" class="btn btn-secondary btn-icon page__homeIcon">
                            <i class="d-block ai-award"></i>
                            {{ __('Awards') }}
                        </a> -->
                        @if (preg_grep("/^forms./i", $accessibleRoutes)) 
                            <a href="{{ $formRoute }}" class="btn btn-secondary btn-icon page__homeIcon">
                                <i class="d-block ai-file-text"></i>
                                {{ __('Forms') }}
                            </a>
                        @endif
                        @if (
                            session()->has('ServiceSelected')
                            && $serviceSession->type === ServiceSelectionTypes::NEO
                            && $serviceSession->plan_subscriptions->plan_id !== PlanType::NEO_FREE_PLAN
                        )
                            <a href="{{ route('workflows.index') }}" class="btn btn-secondary btn-icon page__homeIcon">
                                @if ($totalCount >= 1)
                                    <i class="d-block ai-layers" style="margin-left:30px;">
                                            <span style="font-size:15px;" class="badge bg-danger">{{ $totalCount }}</span>
                                    </i>
                                    {{ __('Workflow') }}
                                @else 
                                    <i class="d-block ai-layers" ></i>
                                    {{ __('Workflow') }}
                                @endif                      
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('components.document-access-service-selection-modal')

@endsection

@push('js')
<script>
$(document).ready(function(){
    let id = $('#document_id').val();
    if (id !== '') {
        $('#documentAccessService').modal('show');
    }

    $('#documentAccessServiceBTN').click(function(){
        let url = "{{ route('home') }}";
        const loader = $('.js-section-loader');

        // Start loader
        loader.removeClass('d-none');

        // Redirect to home page
        document.location.href=url;
    });

    $('table.table tr').click(function () {
        let service = $(this).data('service');
        let id = $(this).data('id');
        let documentID = $('#document_id').val();
        const loader = $('.js-section-loader');
        const data = {
            document_id: documentID,
            service: service
        };

        // Start loader
        loader.removeClass('d-none');

        // Add field based on the service
        switch(service) {
            case 'NEO':
                data['neo_id'] = id;
                break;
            case 'RIO':
                data['rio_id'] = id;
                break;
            default:
                break;
        }

        // Change service selected and fetch URL to redirect to
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: "/document/shared-link/redirect",
            data,
            beforeSend: function(request) {
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                let lang = $('html').attr('lang');
                request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                request.setRequestHeader('Accept-Language', lang);
            },
            success: function(data) {
                let redirectURL = data.data;
                redirectURL = redirectURL.replace(':id', documentID);

                if(redirectURL.includes('download')){
                    let isOpened = window.open(redirectURL, '_blank');
                    if(isOpened){
                        $('#documentAccessServiceBTN').click();
                    }
                } else {
                    document.location.href = redirectURL;
                }
            },
            error: function(error) {
                console.error(error);
                loader.addClass('d-none');
            },
        });
    });
});

$(function () {
    $('#select-service').on('change', function() {
       let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
       let convertId = $(this).children(":selected").attr("id");
       let url
        switch(this.value) {
            case 'NEO':
                url = "{{ route('neo.profile.update.service-selection') }}"
                break;
            case 'RIO':
                url = "{{ route('rio.profile.update.service-selection') }}"
                break;
            default:
        }
        fetch(url, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken
            },
            method: 'POST',
            credentials: "same-origin",
            body: JSON.stringify({
                id: convertId,
            })
        })
        .then(response => {
            //Enable button after receiving response
            if (response.ok) {
                return response.json();
            }
        })
        .then(data => {
            if(data){
                //Update homepage data base on selected service
                //PC
                $("#service-selected-name").text(data.data.name);
                $("#current-service-selected").attr("href", data.data.link);
                //SP
                $("#service-name").text(data.data.name);
                $("#current-service").attr("href", data.data.link);
                
                //Update PC and SP sidebar base on selected service
                //PC
                $("#pc-sidebar-current-service-profile-picture").attr("src", data.data.profile_image);
                $("#pc-sidebar-current-service-profile-picture").attr("alt", data.data.name);
                $("#pc-sidebar-current-service-name").text(data.data.name);
                $("#pc-sidebar-current-service-profile-edit").attr("href", data.data.profile_edit_link);
                $("#pc-sidebar-current-service-privacy-edit").attr("href", data.data.privacy_edit_link);

                //SP
                $("#sp-sidebar-current-service-profile-picture").attr("src", data.data.profile_image);
                $("#sp-sidebar-current-service-profile-picture").attr("alt", data.data.name);
                $("#sp-sidebar-current-service-name").text(data.data.name);
                $("#sp-sidebar-current-service-profile-edit").attr("href", data.data.profile_edit_link);
                $("#sp-sidebar-current-service-privacy-edit").attr("href", data.data.privacy_edit_link);

                switch(this.value) {
                    case 'NEO':
                        //PC
                        $("#pc-sidebar-current-service-information-edit").addClass('d-none');
                        $("#pc-sidebar-current-service-others-header").addClass('d-none');
                        $("#pc-sidebar-current-service-introduce-hero").addClass('d-none');
                        $("#pc-sidebar-current-service-basic-settings").addClass('d-none');

                        //SP
                        $("#sp-sidebar-current-service-information-edit").addClass('d-none');
                        $("#sp-sidebar-current-service-others-header").addClass('d-none');
                        $("#sp-sidebar-current-service-introduce-hero").addClass('d-none');
                        $("#sp-sidebar-current-service-basic-settings").addClass('d-none');
                        break;
                    case 'RIO':
                        //PC
                        $("#pc-sidebar-current-service-information-edit").removeClass('d-none');
                        $("#pc-sidebar-current-service-others-header").removeClass('d-none');
                        $("#pc-sidebar-current-service-introduce-hero").removeClass('d-none');
                        $("#pc-sidebar-current-service-basic-settings").removeClass('d-none');

                        //SP
                        $("#sp-sidebar-current-service-information-edit").removeClass('d-none');
                        $("#sp-sidebar-current-service-others-header").removeClass('d-none');
                        $("#sp-sidebar-current-service-introduce-hero").removeClass('d-none');
                        $("#sp-sidebar-current-service-basic-settings").removeClass('d-none');
                        break;
                    default:
                }
            }
            location.reload();
        })
        .catch(function(error) {
            console.log(error);
        });
    });
});
</script>
@endpush