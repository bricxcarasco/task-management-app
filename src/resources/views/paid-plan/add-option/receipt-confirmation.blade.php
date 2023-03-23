@extends('layouts.main')

@section('content')
<x-confirmation-section>
    <x-slot name="title">
       {{ __('領主書オプション') }}
    </x-slot>

    <!-- Delivery Note Confirmation -->
    <receipt-confirmation />
</x-confirmation-section>  
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
