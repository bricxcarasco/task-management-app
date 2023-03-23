@php
$active_tab = $active_tab ?? null;
$isManageActive = '';
$isFindActive = '';
$isGroupActive = '';

switch ($active_tab) {
    case 'manage':
        $isManageActive = ' active';
        break;
    case 'find':
        $isFindActive = ' active';
        break;
    case 'group':
        $isGroupActive = ' active';
        break;
    default:
        $isManageActive = ' active';
        break;
}
@endphp

<div class="position-relative">
    <h3 class="py-3 mb-0 text-center">
        {{ __('User connection header', [
            'fullname' => $fullname,
        ]) }}
    </h3>

    {{-- Tabs --}}
    <ul class="connection__links p-0">
        <li class="connection__link bg-light {{ $isManageActive }}">
            <a href="{{ route('connection.connection-list') }}">{{ __('Connection') }}<br>{{ __('Manage') }}</a>
        </li>
        <li class="connection__link bg-light {{ $isFindActive }}">
            <a href="{{ route('connection.search.search') }}">{{ __('Connection search') }}<br>{{ __('Expert search') }}</a>
        </li>
        <li class="connection__link bg-light {{ $isGroupActive }}">
            <a href="{{ route('connection.groups.index') }}">
                {{ __('Connection') }}<br>{{ __('Group') }}
            </a>
        </li>
    </ul>
</div>
