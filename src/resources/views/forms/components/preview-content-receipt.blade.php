@php
use App\Helpers\CommonHelper;
@endphp

@push('css')
    <link rel="stylesheet" media="screen" href="{{ asset('css/form-preview.css') }}">
@endpush

<div class="preview">
    <table class="preview__table preview__table--receipt preview__table--no-border table">
        <tbody>
            <tr>
                <td colspan="4"
                    class="preview__table-supplier preview__table-supplier--receipt preview__table--bottom-line">
                    {{ $form->supplier_name ?? $form->connected_supplier_name }}
                </td>
                <td colspan="4" class="preview__table-supplier text-left">
                    {{ __('You') }}
                </td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="8" class="preview__header preview__header--receipt">
                    {{ __('Receipt') }}
                </td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="2" class="preview__table-amount preview__table--bottom-line text-center">
                    <span class="text-justify">{{ __('Amount') }}</span>
                </td>
                <td colspan="4" class="preview__table-price preview__table--bottom-line text-left">
                    {{ __('Yen currency') }}
                    {{ CommonHelper::priceFormat($form->price) }}
                    {{ __('Tax included') }}
                </td>
                <td colspan="2" class="preview__table--bottom-line"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table-h20">
                <td colspan="2" class="preview__table-label text-center">
                    <span class="text-justify">{{ __('However') }}</span>
                    <span class="float-right">：</span>
                </td>
                <td colspan="4" class="text-left">
                    {{ $form->title }}
                </td>
                <td colspan="2"></td>
            </tr>
            <tr class="preview__table-h20">
                <td colspan="2" class="preview__table-label text-center">
                    <span class="text-justify">{{ __('Reference') }}</span>
                    <span class="float-right">：</span>
                </td>
                <td colspan="4" class="text-left">
                    {{ $form->refer_receipt_no }}
                </td>
                <td colspan="2"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table-row--receipt">
                <td colspan="2" class="preview__table-date text-right">
                    {{ __('Date') }}
                </td>
                <td colspan="4" class="text-left">
                    {{ __('Mentioned above, received') }}
                </td>
                <td colspan="2"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table--blank-row">
                <td colspan="8"></td>
            </tr>
            <tr class="preview__table-row--receipt">
                <td colspan="2" class="preview__table-data--sm text-right">
                    {{ __('Company name') }}
                </td>
                <td colspan="4" class="text-left">
                    {{ $form->issuer_name }}
                </td>
                <td colspan="2"></td>
            </tr>
            <tr class="preview__table-row--receipt">
                <td colspan="2" class="preview__table-data--sm text-right">
                    {{ __('Business Address') }}
                </td>
                <td colspan="4" class="text-left">
                    {{ $form->issuer_address }}
                </td>
                <td colspan="2"></td>
            </tr>
            <tr class="preview__table-row--receipt">
                <td colspan="2" class="preview__table-data--sm text-right">
                    {{ __('Telephone No.') }}
                </td>
                <td colspan="4" class="text-left">
                    {{ $form->issuer_tel }}
                </td>
                <td colspan="2"></td>
            </tr>
            <tr class="preview__table-row--receipt">
                <td colspan="2" class="preview__table-data--sm text-right">
                    {{ __('Office registration number') }}
                </td>
                <td colspan="4" class="text-left">
                    {{ $form->issuer_business_number }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</div>
