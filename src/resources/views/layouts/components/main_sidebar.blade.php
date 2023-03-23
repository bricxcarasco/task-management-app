@php
    use App\Services\CordovaService;

    $pcSidebarCurrentService = json_decode(session()->get('ServiceSelected')) ?? null;
@endphp

<div class="bg-light rounded-3 shadow-lg">
    @if (session()->has('ServiceSelected') && $pcSidebarCurrentService->type === 'NEO')
        <div class="px-4 py-4 mb-1 text-center"><img id="pc-sidebar-current-service-profile-picture" class="d-block rounded-circle mx-auto my-2 img--profile_image" src="{{ asset(((auth()->user()->rio->neos->where('id', $pcSidebarCurrentService->data->id)->first())->neo_profile->profile_image) ?? 'images/profile/user_default.png') }}" alt="{{ $pcSidebarCurrentService->data->organization_name }}" onerror="this.src='{{ asset('images/profile/user_default.png') }}'" width="110">
            <h6 id="pc-sidebar-current-service-name" class="mb-0 pt-1">{{ $pcSidebarCurrentService->data->organization_name }}</h6>
        </div>
    @else
        <div class="px-4 py-4 mb-1 text-center"><img id="pc-sidebar-current-service-profile-picture" class="d-block rounded-circle mx-auto my-2 img--profile_image" src="{{ asset(auth()->user()->rio->rio_profile->profile_image) }}" alt="{{ auth()->user()->rio->full_name }}" onerror="this.src='{{ asset('images/profile/user_default.png') }}'" width="110">
            <h6 id="pc-sidebar-current-service-name" class="mb-0 pt-1">{{ auth()->user()->rio->full_name }}</h6>
        </div>
    @endif
    <div class="d-lg-block collapsed pb-2">
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
        <a id="pc-sidebar-current-service-profile-edit" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0" href="{{ (session()->has('ServiceSelected') && $pcSidebarCurrentService->type === 'NEO') ? route('neo.profile.edit', ['neo' => $pcSidebarCurrentService->data->id]) : route('rio.profile.edit') }}">
            {{ __('Edit Profile') }}
        </a>
        <a id="pc-sidebar-current-service-information-edit" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0 {{ (session()->has('ServiceSelected') && $pcSidebarCurrentService->type === 'NEO') ? 'd-none' : '' }}" href="{{ route('rio.information.edit') }}">
            {{ __('Edit Account Information') }}
        </a>
        <a id="pc-sidebar-current-service-privacy-edit" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0" href="{{ (session()->has('ServiceSelected') && $pcSidebarCurrentService->type === 'NEO') ? route('neo.privacy.edit', ['neo' => $pcSidebarCurrentService->data->id]) : route('rio.privacy.edit') }}">{{ __('Privacy Settings') }}</a>
        <h3 id="pc-sidebar-current-service-others-header" class="d-block bg-secondary fs-sm fw-semibold text-muted mb-0 px-4 py-3 {{ (session()->has('ServiceSelected') && $pcSidebarCurrentService->type === 'NEO') ? 'd-none' : '' }}">その他</h3>
        <a id="pc-sidebar-current-service-basic-settings" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0 {{ (session()->has('ServiceSelected') && $pcSidebarCurrentService->type === 'NEO') ? 'd-none' : '' }}" href="{{ route('basic-settings') }}">{{ __('Basic Settings') }}</a>
        <!-- <a class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0" href="#">{{ __('Help') }}</a> -->
        <a id="pc-sidebar-current-service-introduce-hero" class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0 {{ (session()->has('ServiceSelected') && $pcSidebarCurrentService->type === 'NEO') ? 'd-none' : '' }}" href="{{ route('rio.introduce_hero.introduce') }}">{{ __('Introduce HERO') }}</a>
        <a class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0 logout-js" href="{{ route('logout') }}">{{ __('Log out') }}</a>
        <!-- <a class="d-flex align-items-center nav-link-style px-4 py-3 border-top mb-0" href="#">退会</a> -->
    </div>
</div>