@extends('layouts.main')

@section('content')
<x-confirmation-section>
    <x-slot name="title">
       {{ __('NEO文書管理オプション') }}
    </x-slot>

    <!-- Document Management Confirmation -->
    <document-management-confirmation />
</x-confirmation-section>  
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
