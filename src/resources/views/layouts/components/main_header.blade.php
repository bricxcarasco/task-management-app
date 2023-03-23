@php
    use App\Enums\ServiceSelectionTypes;
    use App\Services\CordovaService;

    $service = json_decode(Session::get('ServiceSelected'));

    $headerBackgroundColor = 'header_rio';
    $iconsNeoColor = '';
    $navbarVariant = 'navbar-light';

    if ($service->type === ServiceSelectionTypes::NEO) {
        $headerBackgroundColor = 'header_neo';
        $iconsNeoColor = 'icons_color_neo';
        $navbarVariant = 'navbar-dark';
    }

    $spSidebarCurrentService = json_decode(session()->get('ServiceSelected')) ?? null;
@endphp
<header class="header {{ $headerBackgroundColor }} navbar navbar-expand-lg {{ $navbarVariant }} navbar-floating navbar-sticky" data-scroll-header
    data-fixed-element>
    <div class="container px-0 px-xl-3">
        <button class="navbar-toggler ms-n2 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#primaryMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand flex-shrink-0 order-lg-1 mx-auto ms-lg-0 pe-lg-2 me-lg-4" href="{{ route('home') }}">
            <img src="{{ asset('around/img/logo.png') }}" alt="HEROロゴ" width="88">
        </a>
        <div class="collapse navbar-collapse order-lg-2" id="navbarCollapse1">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link nav-tooltip" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Home Screen') }}" href="/">
                        <i class="fs-xl {{ $iconsNeoColor }} ai-home"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-tooltip" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Chat') }}" href="{{ route('chat.room.index') }}">
                        <i class="fs-xl {{ $iconsNeoColor }} ai-message-square"></i>
                        @if ($messagesCount > 0)
                            <span class="badge bg-danger">{{ $messagesCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-tooltip" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Schedule') }}" href="{{ route('schedules.index') }}">
                        <i class="fs-xl {{ $iconsNeoColor }} ai-calendar"></i>
                        @if ($invitationsCount > 0)
                            <span class="badge bg-danger">{{ $invitationsCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-tooltip" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Tasks') }}" href="{{ route('tasks.index') }}">
                        <i class="fs-xl {{ $iconsNeoColor }} ai-list"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="d-flex align-items-center order-lg-3 ms-lg-auto">
            <div class="dropdown dropdown--notification me-3 nav-tooltip" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('Notification') }}">
                <button class="border-0 p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="background: transparent;color: #4a4b65;">
                    <i class="ai-bell {{ $iconsNeoColor }} fs-xl align-middle"></i>
                    @if ($unreadNotifCount > 0)
                        <span class="badge notification-badge bg-danger">
                            {{ $unreadNotifCount }}
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <h6 class="dropdown-header mb-0">{{ __('Notification') }}</h6>
                    @if ($notifications->isNotEmpty())
                        @foreach ($notifications as $notification)
                            <a data-url="{{ route('notifications.read', $notification->id) }}"
                                class="js-read-notif hoverable dropdown-item pe-5
                            {{ $notification->is_notification_read ? '' : 'unread' }}">
                                <span
                                    class="text-muted fs-xs d-block">{{ $notification->notification_recipient }}</span>
                                <span>{{ $notification->notification_content }}</span>
                                <p class="m-0 fs-xs text-muted">{{ $notification->notification_date }}</p>
                            </a>
                        @endforeach
                    @else
                        <p class="text-center text-sm mt-4 mx-2">
                            {{ __('No notifications') }}
                        </p>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="dropdown-item text-center btn-link">
                        {{ __('See all notifications') }}
                    </a>
                </div>
            </div>
            <button class="btn p-0 {{ $iconsNeoColor ?? 'nav-link-style' }} d-block d-md-none nav-tooltip" type="button" data-bs-toggle="modal"
                data-bs-target="#service_switch" data-bs-toggle="tooltip" title="{{ __('Services') }}">
                <i class="ai-users {{ $iconsNeoColor }} fs-xl align-middle"></i>
            </button>
        </div>
    </div>
</header>
<div class="offcanvas  order-lg-2" id="primaryMenu">
    <div class="offcanvas-header pb-0 justify-content-end">
        <button class="btn-close lead" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    @if (session()->has('ServiceSelected') && $spSidebarCurrentService->type === 'NEO')
        <div class="px-4 py-4 mb-1 text-center"><img id="sp-sidebar-current-service-profile-picture" class="d-block rounded-circle mx-auto my-2 img--profile_image" src="{{ asset(((auth()->user()->rio->neos->where('id', $spSidebarCurrentService->data->id)->first())->neo_profile->profile_image) ?? 'images/profile/user_default.png') }}" alt="{{ $spSidebarCurrentService->data->organization_name }}" width="110">
            <h6 id="sp-sidebar-current-service-name" class="mb-0 pt-1">{{ $spSidebarCurrentService->data->organization_name }}</h6>
        </div>
    @else
        <div class="px-4 py-4 mb-1 text-center"><img id="sp-sidebar-current-service-profile-picture" class="d-block rounded-circle mx-auto my-2 img--profile_image" src="{{ asset(auth()->user()->rio->rio_profile->profile_image) }}" alt="{{ auth()->user()->rio->full_name }}" width="110">
            <h6 id="sp-sidebar-current-service-name" class="mb-0 pt-1">{{ auth()->user()->rio->full_name }}</h6>
        </div>
    @endif
    <div class="d-lg-block collapsed pb-2" id="account-menu">
        <a class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0" href="/">
            ホーム画面
        </a>
        {{-- Cordova Assets --}}
        @if(!CordovaService::hasCookie())
            <h3 class="d-block d-flex align-items-center bg-secondary fs-sm fw-semibold text-muted mb-0 px-4 py-3">
                {{ __('Account') }}
                <span class="text-muted fs-sm fw-normal ms-auto badge bg-dark ">{{ __('Free Membership') }}</span>
            </h3>
        @endif
        <a id="sp-sidebar-current-service-profile-edit" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0"
            href="{{ (session()->has('ServiceSelected') && $spSidebarCurrentService->type === 'NEO') ? route('neo.profile.edit', ['neo' => $spSidebarCurrentService->data->id]) : route('rio.profile.edit') }}">{{ __('Edit Profile') }}</a>
        <a id="sp-sidebar-current-service-information-edit" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0 {{ (session()->has('ServiceSelected') && $spSidebarCurrentService->type === 'NEO') ? 'd-none' : '' }}" href="{{ route('rio.information.edit') }}"
            href="{{ route('rio.information.edit') }}">{{ __('Edit Account Information') }}</a>
        <a id="sp-sidebar-current-service-privacy-edit" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0"
            href="{{ (session()->has('ServiceSelected') && $spSidebarCurrentService->type === 'NEO') ? route('neo.privacy.edit', ['neo' => $spSidebarCurrentService->data->id]) : route('rio.privacy.edit') }}">{{ __('Privacy Settings') }}</a>
        <h3 id="sp-sidebar-current-service-others-header" class="d-block bg-secondary fs-sm fw-semibold text-muted mb-0 px-4 py-3 {{ (session()->has('ServiceSelected') && $spSidebarCurrentService->type === 'NEO') ? 'd-none' : '' }}">その他</h3>
        <a id="sp-sidebar-current-service-basic-settings" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0 {{ (session()->has('ServiceSelected') && $spSidebarCurrentService->type === 'NEO') ? 'd-none' : '' }}"
            href="{{ route('basic-settings') }}">{{ __('Basic Settings') }}</a>
        <!-- <a class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0" href="#">{{ __('Help') }}</a> -->
        <a id="sp-sidebar-current-service-introduce-hero" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0 {{ (session()->has('ServiceSelected') && $spSidebarCurrentService->type === 'NEO') ? 'd-none' : '' }}"
            href="{{ route('rio.introduce_hero.introduce') }}">{{ __('Introduce HERO') }}</a>
        <a class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0 logout-js"
            href="{{ route('logout') }}">{{ __('Log out') }}</a>
        <!-- <a class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0" href="#">退会</a> -->
    </div>
</div>
<div class="modal fade" id="service_switch" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title ">{{ __('Service subject selection') }}</h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="switch">
                    <x-service-selection />
                </div>
                <div class="py-5 text-center">
                    <a href="{{ route('neo.registration.index') }}" class="nav-link">
                        + {{ __('Make a new NEO') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="{{ asset('js/notifications.js') }}" defer></script>
@endpush
