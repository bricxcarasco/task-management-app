@extends('layouts.main')

@section('content')
<x-confirmation-section>
    <x-slot name="title">
       {{ __('増員オプション') }}
    </x-slot>

    <!-- Add Staff Confirmation -->
    <add-staff-confirmation />
</x-confirmation-section>  
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
