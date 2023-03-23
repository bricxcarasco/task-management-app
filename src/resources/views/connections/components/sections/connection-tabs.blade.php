<h3 class="py-3 mb-0 text-center">{{ auth()->user()->rio->full_name }}'s {{ __('Connection') }}</h3>
<ul class="connection__links p-0">
    <li class="connection__link bg-light">
        <a href="#">{{ __('Connection') }}<br>{{ __('Management') }}</a>
    </li>
    <li class="connection__link bg-light {{ request()->routeIs('connection.search.search') || request()->routeIs('connection.search.result') ? 'active' : '' }}">
        <a href="{{ route('connection.search.search') }}"
            class="nav-link px-md-4 px-0 text-center {{ request()->routeIs('connection.search.search') || request()->routeIs('connection.search.result') ? 'active' : '' }}">
            {{ __('Connection') }}<br>{{ __('Search for') }}
        </a>
    </li>
    <li class="connection__link bg-light">
        <a href="#">{{ __('Connection') }}<br>{{ __('Group') }}</a>
    </li>
</ul>