@php
$active_tab = $active_tab ?? null;
$isListActive = '';
$isRequestActive = '';

switch ($active_tab) {
    case 'list':
        $isListActive = ' active';
        break;
    case 'request':
        $isRequestActive = ' active';
        break;
    default:
        $isListActive = ' active';
        break;
}

@endphp

<ul class="nav nav-tabs tabs--custom d-flex" role="tablist">
    <li class="nav-item m-0">
        <a href="{{ route('connection.connection-list') }}" class="nav-link px-md-4 px-0 text-center {{ $isListActive }}">
            {{ __('Connection List') }}
        </a>
    </li>
    <li class="nav-item m-0">
        <a href="{{ route('connection.application-list') }}" class="nav-link px-md-4 px-0 text-center {{ $isRequestActive }}">
            {{ __('Application Request') }}
        </a>
    </li>
</ul>