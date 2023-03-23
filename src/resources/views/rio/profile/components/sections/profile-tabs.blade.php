<ul class="nav nav-tabs tabs--custom d-flex" role="tablist">
    {{-- Profile - Introduction --}}
    <li class="nav-item m-0">
        <a href="{{ route('rio.profile.introduction', [$rio->id, 'is_access_in_profile' => true]) }}"
            class="nav-link px-md-4 px-0 text-center {{ request()->routeIs('rio.profile.introduction') ? 'active' : '' }}">
            <i class="fi-home me-2"></i>
            {{ __('Introduction') }}
        </a>
    </li>
    {{-- Profile - Information --}}
    <li class="nav-item m-0">
        <a href="{{ route('rio.profile.information', [$rio->id, 'is_access_in_profile' => true]) }}"
            class="nav-link px-md-4 px-0 text-center {{ request()->routeIs('rio.profile.information') ? 'active' : '' }}">
            <i class="fi-home me-2"></i>
            {{ __('Information') }}
        </a>
    </li>
</ul>