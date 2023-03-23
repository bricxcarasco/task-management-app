@php
$active_tab = $active_tab ?? null;
$isParticipateActive = '';
$isInvitationActive = '';

switch ($active_tab) {
    case 'participating':
        $isParticipateActive = ' active';
        break;
    case 'invitation':
        $isInvitationActive = ' active';
        break;
    default:
        $isParticipateActive = ' active';
        break;
}
@endphp

<ul class="nav nav-tabs tabs--custom d-flex" role="tablist">
    <li class="nav-item m-0">
        <a href="{{ route('connection.groups.index') }}"
            class="nav-link px-md-4 px-0 text-center {{ $isParticipateActive }}">
            {{ __('Participating') }}
        </a>
    </li>
    <li class="nav-item m-0">
        <a href="{{ route('connection.groups.invitations') }}"
            class="nav-link px-md-4 px-0 text-center {{ $isInvitationActive }}">
            {{ __('Invitation Request') }}
        </a>
    </li>
</ul>
