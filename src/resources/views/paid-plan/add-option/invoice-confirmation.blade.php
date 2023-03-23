@extends('layouts.main')

@section('content')
<x-confirmation-section>
    <x-slot name="title">
       {{ __('請求書オプション') }}
    </x-slot>

    <!-- Invoice Confirmation -->
    <invoice-confirmation />
</x-confirmation-section>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
