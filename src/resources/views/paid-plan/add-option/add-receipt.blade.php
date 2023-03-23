@extends('layouts.main')

@section('content')
<x-add-option-section>
    <x-slot name="title">
       {{ __('領主書オプション') }}
    </x-slot>

    <!-- Add Receipt -->
    <add-receipt />
</x-add-option-section> 
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
