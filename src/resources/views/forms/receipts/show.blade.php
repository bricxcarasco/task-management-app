@php
use App\Enums\Form\ProductTaxDistinction;
use App\Helpers\CommonHelper;
@endphp

@extends('layouts.main')

@section('content')
    {{-- Flash Alert --}}
    @include('components.alert')

    {{-- Delete modal --}}
    @include('forms.components.delete-modal')

    {{-- Preview modal --}}
    @include('forms.components.preview-modal')

    {{-- Document management linkage modal --}}
    @include('forms.components.document-management-modal')

    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3">
        <div class="row">
            <div class="col-12 col-md-9 offset-md-3">
                <div class="d-flex align-items-center justify-content-between border-bottom">
                    <a href="{{ route('forms.receipts.index') }}" class="btn btn-link">
                        <i class="ai-arrow-left"></i>
                    </a>
                    <div class="d-flex align-items-center justify-content-around">
                        <button type="button" class="btn btn-link " data-bs-toggle="modal"
                            data-bs-target="#form-document-linkage">{{ __('Send to Document Management') }}</button> 
                        <a href="{{ route('forms.receipts.update-history', $form->id) }}" class="btn btn-link p-0 me-3">{{ __('History2') }}</a>
                        <a href="{{ route('forms.receipts.edit', $form->id) }}" 
                            class="btn btn-link p-0 me-3">{{ __('Edit') }}</a>
                        <button type="button" class="btn btn-link js-delete-form"
                            data-action="{{ route('forms.receipts.destroy', ['form' => $form->id, 'withAlert' => true]) }}">
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#form-preview">
                        {{ __('Form Preview') }}
                    </button>
                    <a href="{{ route('forms.pdf-download', $form->id) }}" target="_blank">
                        {{ __('PDF Download') }}
                    </a>
                </div>

                {{-- Form information --}}
                <p class="mb-0 bg-dark-gray p-2 c-white">{{ __('Basic Information') }}</p>
                <div class="p-4">
                    <table class="table table-striped table--quotation">
                        <tbody>
                            @php
                                $amount = CommonHelper::priceFormat($form->price);
                            @endphp
                            <tr>
                                <th>{{ __('However write') }}</th>
                                <td>{{ $form->title }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Suppliers') }}</th>
                                <td>{{ $form->supplier_name ?? $form->connected_supplier_name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Issue Date') }}</th>
                                <td>{{ $form->issue_date }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Receipt date') }}</th>
                                <td>{{ $form->receipt_date }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Receipt amount') }}</th>
                                <td>{{ $amount }}{{ __('Yen') }} {{ __('Tax included') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Reference invoice no.') }}</th>
                                <td>{{ $form->refer_receipt_no }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Basic settings section --}}
                <p class="mb-0 bg-dark-gray p-2 c-white">{{ __('Issuer Information') }}</p>
                <div class="p-4">
                    <table class="table table-striped table--quotation">
                        <tbody>
                            <tr class="bg-blue">
                                <th>{{ __('Store name / Trade name') }}</th>
                                <td>{{ $form->issuer_name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Address2') }}</th>
                                <td>{{ $form->issuer_address }}</td>
                            </tr>
                            <tr>
                                <th>TEL</th>
                                <td>{{ $form->issuer_tel }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Business Number') }}</th>
                                <td>{{ $form->issuer_business_number }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ mix('js/dist/forms/index.js') }}" defer></script>

    <script>
        $(function() {
            $('.js-delete-form').on('click', function() {
                let formSelector = $('#delete-form');
                formSelector.attr('action', $(this).data('action'));
                $('#form-delete-modal').modal('show');
            });
        });
    </script>
@endpush
