<ul class="nav nav-tabs nav-fill" role="tablist">
    <li class="nav-item m-0">
        <a href="{{ route('neo.profile.participants', ['neo' => $neo->id ]) }}" class="nav-link px-md-4 px-0 text-center {{ $active == __('Application for participation') ? 'active' : '' }}">{{ __('Application for participation') }}</a>
    </li>
    <li class="nav-item m-0">
        <a href="{{ route('neo.profile.invitation', ['neo' => $neo->id ]) }}" class="nav-link px-md-4 px-0 text-center {{ $active == __('Inviting') ? 'active' : '' }}">{{ __('Inviting') }}</a>
    </li>
</ul>