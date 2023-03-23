@extends('layouts.main')

@section('content')
<x-add-option-section>
    <x-slot name="title">
       {{ __('ネットショップオプション') }}
    </x-slot>

    <!-- Add Net Shop -->
    <add-net-shop />
</x-add-option-section> 
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
