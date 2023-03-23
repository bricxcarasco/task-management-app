@php
$active_tab = $active_tab ?? null;
$isPersonalActive = '';
$isSharedActive = '';
$isExternalActive = '';

switch ($active_tab) {
    case 'personal':
        $isPersonalActive = ' active';
        break;
    case 'shared':
        $isSharedActive = ' active';
        break;
    case 'external':
        $isExternalActive = ' active';
        break;
    default:
        $isPersonalActive = ' active';
        break;
}
@endphp

{{-- Tabs --}}
<ul class="nav nav-tabs tabs--custom d-flex mt-3 mb-0" role="tablist">
    <li class="nav-item m-0">
        <a href="{{ route('document.default-list') }}" class="nav-link px-md-4 px-0 text-center {{ $isPersonalActive }}">
            {{ __('My Documents') }}
        </a>
    </li>
    <li class="nav-item m-0">
        <a href="{{ route('document.shared-list') }}" class="nav-link px-md-4 px-0 text-center {{ $isSharedActive }}">
            {{ __('Shared') }}
        </a>
    </li>
    {{-- <li class="nav-item m-0 {{ $isExternalActive }}">
        <a href="#" class="nav-link px-md-4 px-0 text-center {{ $isExternalActive }}">
            {{ __('External') }}
        </a>
    </li> --}}
</ul>
