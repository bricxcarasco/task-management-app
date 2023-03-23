<ul class="nav nav-tabs tabs--custom d-flex" role="tablist">
    <li class="nav-item m-0">
        <a href="{{ route('neo.profile.participants-list', $neo->id) }}"
            class="nav-link px-md-4 px-0 text-center {{ $active == __('Participant List') ? 'active' : '' }}">
            {{ __('Participant List') }}
        </a>
    </li>
    <li class="nav-item m-0">
        <a href="{{ route('neo.profile.groups-list', $neo->id) }}"
            class="nav-link px-md-4 px-0 text-center {{ $active == __('Group List') ? 'active' : '' }}">
            {{ __('Group List') }}
        </a>
    </li>
</ul>
