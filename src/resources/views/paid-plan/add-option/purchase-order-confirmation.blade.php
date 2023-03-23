@extends('layouts.main')

@section('content')
<x-confirmation-section>
    <x-slot name="title">
       {{ __('発注書オプション') }}
    </x-slot>

    <!-- Purchase Order Confirmation -->
    <purchase-order-confirmation />
</x-confirmation-section> 
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
