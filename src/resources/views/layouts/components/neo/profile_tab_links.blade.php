<ul class="nav nav-tabs tabs--custom d-flex">
    <li class="nav-item m-0">
        <a href="{{ route('neo.profile.introduction', [$neo->id, 'is_access_in_profile' => true]) }}"
            class="nav-link px-md-4 px-0 text-center {{ $active == __('Introduction') ? 'active' : '' }}">
            {{ __('Introduction') }}</a>
    </li>
    <li class="nav-item m-0">
        <a href="{{ route('neo.profile.information', [$neo->id, 'is_access_in_profile' => true]) }}" class="nav-link px-md-4 px-0 text-center {{ $active == __('Information')  ? 'active' : '' }}">{{ __('Information') }}</a>
    </li>
    <li class="nav-item m-0">
        <a href="{{ route('neo.profile.participants-list', [$neo->id, 'is_access_in_profile' => true]) }}"
            class="nav-link px-md-4 px-0 text-center {{ $active == __('Participant') ? 'active' : '' }}">
            {{ __('Participant') }}</a>
    </li>
</ul>
