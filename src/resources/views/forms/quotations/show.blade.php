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
    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 break-word">
        <div class="row">
            <div class="col-12 col-md-9 offset-md-3">
                <div class="d-flex align-items-center justify-content-between border-bottom">
                    <a href="{{ route('forms.quotations.index') }}" class="btn btn-link">
                        <i class="ai-arrow-left"></i>
                    </a>
                    <div class="d-flex align-items-center justify-content-around">
                        <button type="button" class="btn btn-link" data-bs-toggle="modal"
                            data-bs-target="#form-document-linkage">{{ __('Send to Document Management') }}</button>
                        <a href="{{ route('forms.quotations.update-history', $form->id) }}" class="btn btn-link p-0 me-3">{{ __('History2') }}</a>
                        <a href="{{ route('forms.quotations.edit', $form->id) }}"
                            class="btn btn-link p-0 me-3">{{ __('Edit') }}</a>
                        <button type="button" class="btn btn-link js-delete-form"
                            data-action="{{ route('forms.quotations.destroy', ['form' => $form->id, 'withAlert' => true]) }}">
                            {{ __('Delete') }}
                        </button>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-link" data-bs-toggle="modal"
                        data-bs-target="#form-preview">{{ __('Form Preview') }}</button>
                    <a href="{{ route('forms.pdf-download', $form->id) }}" target="_blank">{{ __('PDF Download') }}</a>
                </div>

                {{-- Form information --}}
                <p class="mb-0 bg-dark-gray p-2 c-white">{{ __('Basic Information') }}</p>
                <div class="p-4">
                    <table class="table table-striped table--quotation">
                        <tbody>
                            <tr class="bg-blue">
                                <th>{{ __('Quotation No') }}</th>
                                <td>{{ $form->form_no }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Subject') }}</th>
                                <td>{{ $form->title }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Suppliers') }}</th>
                                <td>{{ $form->supplier_name ?? $form->connected_supplier_name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Postal code') }}</th>
                                <td>{{ $form->zipcode }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Address2') }}</th>
                                <td>{{ $form->address }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Issue Date') }}</th>
                                <td>{{ $form->issue_date }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Expiration Date') }}</th>
                                <td>{{ $form->expiration_date }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Delivery Address') }}</th>
                                <td>{{ $form->delivery_address }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Delivery Deadline') }}</th>
                                <td>{{ $form->delivery_date }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Payment Terms') }}</th>
                                <td>{{ $form->payment_terms }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Form products section --}}
                @if ($form->products->isNotEmpty())
                    <p class="mb-0 bg-dark-gray p-2 c-white">{{ __('Item') }}</p>
                    <div class="p-4">
                        <ol>
                            @foreach ($form->products as $product)
                                @php
                                    $unitPrice = CommonHelper::priceFormat($product->unit_price);
                                    $amount = CommonHelper::priceFormat($product->amount);
                                    
                                    switch ($product->tax_distinction) {
                                        case ProductTaxDistinction::PERCENT_10:
                                            $taxDistinction = '10%';
                                            break;
                                        case ProductTaxDistinction::REDUCTION_8_PERCENT:
                                            $taxDistinction = '8%';
                                            break;
                                        default:
                                            $taxDistinction = '0%';
                                            break;
                                    }
                                @endphp

                                <li>
                                    <table class="table bg-gray">
                                        <tbody>
                                            <tr>
                                                <th class="w-md-25">{{ __('Product name 2') }}：</th>
                                                <td>{{ $product->name }}</td>
                                            </tr>
                                            <tr>
                                                <th class="w-md-25">{{ __('Quantity') }}：</th>
                                                <td>{{ $product->quantity ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <th class="w-md-25">{{ __('Unit') }}：</th>
                                                <td>{{ $product->unit }}</td>
                                            </tr>
                                            <tr>
                                                <th class="w-md-25">{{ __('Unit Price') }}：</th>
                                                <td>{{ $unitPrice }}{{ __('Yen') }}</td>
                                            </tr>
                                            <tr>
                                                <th class="w-md-25">{{ __('Amount') }}：</th>
                                                <td>{{ $amount }}{{ __('Yen') }}</td>
                                            </tr>
                                            <tr>
                                                <th class="w-md-25">{{ __('Tax Distinction') }}：</th>
                                                <td>{{ $taxDistinction }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </li>
                            @endforeach
                        </ol>

                        @if (!empty($pricesAndTaxes))
                            <div class="mt-4">
                                <table class="mx-auto table--border-0">
                                    <tr>
                                        <td>{{ __('Sub Total') }}</td>
                                        <td>：{{ CommonHelper::priceFormat($pricesAndTaxes->total_price) }}{{ __('Yen') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('GST (10%)') }}</td>
                                        <td>：{{ CommonHelper::priceFormat($pricesAndTaxes->tax_10_percent) }}{{ __('Yen') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Consumption tax (Reduction 8%)') }}</td>
                                        <td>：{{ CommonHelper::priceFormat($pricesAndTaxes->tax_8_percent) }}{{ __('Yen') }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <p class="fs-4 mt-3 text-center">
                                <span>{{ __('Total') }}：</span>
                                {{ CommonHelper::priceFormat($pricesAndTaxes->overall_total) }}{{ __('Yen') }}
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Remarks --}}
                @if (!empty($form->remarks))
                    <p class="mb-0 bg-dark-gray p-2 c-white">{{ __('Remarks') }}</p>
                    <div class="p-4">
                        <ul class="list-style-none">
                            <li>{!! nl2br(e($form->remarks)) ?? '' !!}</li>
                        </ul>
                    </div>
                @endif

                {{-- Basic settings section --}}
                @include('forms.components.basic-settings')
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
