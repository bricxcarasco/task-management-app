@extends('layouts.main')

@section('content')
<x-confirmation-section>
    <x-slot name="title">
       {{ __('発注書オプション') }}
    </x-slot>

    <!-- Delivery Note Confirmation -->
    <delivery-note-confirmation />
</x-confirmation-section>   
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
