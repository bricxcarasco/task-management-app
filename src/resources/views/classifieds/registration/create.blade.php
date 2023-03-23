@extends('layouts.main')

@section('content')
    <form method="POST" class="js-product-form" action="{{ route('classifieds.sales.confirmation') }}" aria-label="{{ __('store') }}" novalidate>
        @csrf
        @method('POST')
        @include('classifieds.registration.form')
    </form>
@endsection
