@extends('layouts.main')

@section('content')
<x-add-option-section>
    <x-slot name="title">
       {{ __('発注書オプション') }}
    </x-slot>

    <!-- Add Delivery Note -->
    <add-delivery-note />
</x-add-option-section> 
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
