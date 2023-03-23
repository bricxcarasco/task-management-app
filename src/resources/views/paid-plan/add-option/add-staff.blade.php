@extends('layouts.main')

@section('content')
<x-add-option-section>
    <x-slot name="title">
       {{ __('増員オプション') }}
    </x-slot>

    <!-- Add Staff -->
    <add-staff />
</x-add-option-section>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
