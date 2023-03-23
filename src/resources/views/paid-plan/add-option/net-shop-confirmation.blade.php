@extends('layouts.main')

@section('content')
<x-confirmation-section>
    <x-slot name="title">
       {{ __('ネットショップオプション') }}
    </x-slot>

    <!-- Net Shop Confirmation -->
    <net-shop-confirmation />
</x-confirmation-section> 
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
