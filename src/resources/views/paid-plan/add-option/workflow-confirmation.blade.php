@extends('layouts.main')

@section('content')
<x-confirmation-section>
    <x-slot name="title">
       {{ __('ワークフローオプション') }}
    </x-slot>

    <!-- Workflow Confirmation -->
    <workflow-confirmation />
</x-confirmation-section> 
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/paid-plan/index.js') }}" defer></script>
@endpush
