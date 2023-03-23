@extends('layouts.main')

@section('content')
<x-add-option-section>
    <x-slot name="title">
       {{ __('NEO文書管理オプション') }}
    </x-slot>

    <!-- Add Document Management -->
    <add-document-management />
</x-add-option-section> 
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
