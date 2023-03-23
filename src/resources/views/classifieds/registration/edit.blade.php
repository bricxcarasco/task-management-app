@extends('layouts.main')

@section('content')
    <form method="POST" class="js-product-form" action="{{ route('classifieds.sales.edit-confirmation', $productId) }}" aria-label="{{ __('edit') }}" novalidate>
        @csrf
        @method('POST')
        @include('classifieds.registration.form')
    </form>
@endsection
