@php
use App\Enums\Form\ProductTaxDistinction;
use App\Helpers\CommonHelper;
@endphp

@push('css')
    <link rel="stylesheet" media="screen" href="{{ asset('css/form-preview.css') }}">
@endpush

<div class="preview">
    <h3 class="preview__header">{{ __('Quotation') }}</h3>

    <div class="preview__content">
        <div class="preview__section position-relative">
            <table class="preview__table table preview__table--no-border">
                <tbody>
                    <tr>
                        <td colspan="9" class="text-left">
                            {{ $form->zipcode }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" class="text-left">
                            {{ $form->address }}
                        </td>
                        <td colspan="3" class="text-left">{{ __('Quotation No') }}：</td>
                        <td colspan="4" class="text-left">{{ $form->form_no }}</td>
                    </tr>
                    <tr class="preview__table--blank-row">
                        <td colspan="16"></td>
                    </tr>
                    <tr>
                        <td colspan="7" rowspan="2"
                            class="preview__table-supplier preview__table--bottom-line p-0">
                            {{ $form->supplier_name ?? $form->connected_supplier_name }}
                        </td>
                        <td colspan="2" rowspan="2"
                            class="preview__table-supplier preview__table--bottom-line p-0 nowrap">
                            {{ __('You') }}
                        </td>
                        <td colspan="3" class="text-left v-align-top">
                            {{ $form->issuer_name }}
                        </td>
                        <td colspan="4" rowspan="3">
                            @if ($form->issuer_detail_logo !== null)
                                <img class="preview__table-image" id="pdf-img" src="{{ $form->issuer_detail_logo }}"
                                    onerror="this.src = '{{ asset('img/default-image.png') }}'">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-left v-align-top">
                            {{ $form->issuer_department_name }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" class="text-right">
                            {{ __('For inquiries, we will quote as follows.') }}
                        </td>
                        <td colspan="7" class="text-left v-align-top">
                            {{ $form->issuer_address }}
                        </td>
                    </tr>
                    <tr class="preview__table--blank-row">
                        <td colspan="16"></td>
                    </tr>
                    <tr class="preview__table--blank-row">
                        <td colspan="16"></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="preview__table--bottom-line p-0 text-center">
                            <span class="text-justify nowrap">{{ __('Subject') }}</span>
                            <span class="float-right">：</span>
                        </td>
                        <td colspan="6" class="preview__table--bottom-line p-0 text-left">
                            {{ $form->title }}
                        </td>
                        <td colspan="3" class="text-left nowrap">ＴＥＬ：</td>
                        <td colspan="4" class="text-left">{{ $form->issuer_tel }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="preview__table--bottom-line p-0 text-center">
                            <span class="text-justify nowrap">{{ __('Offer amount') }}</span>
                            <span class="float-right">：</span>
                        </td>
                        <td colspan="6" class="preview__table--bottom-line p-0 text-center font-bold">
                            {{ __('Yen currency') }}
                            {{ CommonHelper::priceFormat($pricesAndTaxes->overall_total) }}
                            {{ __('Tax included') }}
                        </td>
                        <td colspan="3" class="text-left nowrap">ＦＡＸ：</td>
                        <td colspan="4" class="text-left">{{ $form->issuer_fax }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="preview__table--bottom-line p-0 text-center">
                            <span class="text-justify nowrap">{{ __('Delivery location') }}</span>
                            <span class="float-right">：</span>
                        </td>
                        <td colspan="6" class="preview__table--bottom-line p-0 text-left">
                            {{ $form->delivery_address }}
                        </td>
                        <td colspan="3" class="text-left nowrap">{{ __('Office registration number') }}：</td>
                        <td colspan="4" class="text-left">{{ $form->issuer_business_number }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="preview__table--bottom-line p-0 text-center">
                            <span class="text-justify nowrap">{{ __('Delivery Date') }}</span>
                            <span class="float-right">：</span>
                        </td>
                        <td colspan="6" class="preview__table--bottom-line p-0 text-left">
                            {{ $form->delivery_date }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="preview__table--bottom-line p-0 text-center">
                            <span class="text-justify nowrap">{{ __('Payment terms') }}</span>
                            <span class="float-right">：</span>
                        </td>
                        <td colspan="6" class="preview__table--bottom-line p-0 text-left">
                            {{ $form->payment_terms }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="preview__table--bottom-line p-0 text-center">
                            <span class="text-justify nowrap">{{ __('Estimated expiration date') }}</span>
                            <span class="float-right">：</span>
                        </td>
                        <td colspan="6" class="preview__table--bottom-line p-0 text-left">
                            {{ $form->expiration_date }}
                        </td>
                    </tr>
                    <tr class="preview__table--blank-row">
                        <td colspan="16"></td>
                    </tr>
                    <tr class="preview__table--blank-row">
                        <td colspan="16"></td>
                    </tr>
                </tbody>
            </table>

            <div class="preview__stamp">
                <div class="preview__stamp-box"></div>
                <div class="preview__stamp-box" style="right: 2px"></div>
                <div class="preview__stamp-box" style="right: 4px"></div>
            </div>
        </div>

        <hr />

        <div class="preview__section">
            <table class="preview__table preview__table--border table">
                <thead class="preview__table-th">
                    <tr>
                        <td class="w-10">NO.</td>
                        <td class="w-25">{{ __('Product / service name') }}</td>
                        <td>{{ __('Unit price') }}</td>
                        <td>{{ __('Quantity') }}</td>
                        <td>{{ __('Amount') }}</td>
                        <td>{{ __('Tax') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @if ($form->products->isNotEmpty())
                        @foreach ($form->products as $key => $product)
                            @php
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

                            <tr>
                                <td class="text-center">
                                    {{ $key + 1 }}
                                </td>
                                <td class="text-left">
                                    {{ $product->name }}
                                </td>
                                <td class="text-right">
                                    {{ __('Yen currency') }}
                                    {{ CommonHelper::priceFormat($product->unit_price) }}
                                </td>
                                <td class="text-center">
                                    {{ $product->quantity }}
                                </td>
                                <td class="text-right">
                                    {{ __('Yen currency') }}
                                    {{ CommonHelper::priceFormat($product->amount) }}
                                </td>
                                <td class="text-center">
                                    {{ $taxDistinction }}
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    @if (!empty($pricesAndTaxes))
                        <tr>
                            <td class="border-none"></td>
                            <td colspan="2" class="preview__table--border-double text-center">
                                {{ __('Total') }}
                            </td>
                            <td class="text-center preview__table--border-double"></td>
                            <td colspan="2" class=" preview__table--border-double text-center">
                                {{ __('Yen currency') }}
                                {{ CommonHelper::priceFormat($pricesAndTaxes->total_price) }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-none"></td>
                            <td colspan="2" class="left-border-none text-center">{{ __('10% consumption tax') }}
                            </td>
                            <td colspan="2" class="text-center">
                                {{ __('Yen currency') }}
                                {{ CommonHelper::priceFormat($pricesAndTaxes->tax_10_percent) }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-none"></td>
                            <td colspan="2" class="left-border-none text-center">{{ __('8% consumption tax') }}
                            </td>
                            <td colspan="2" class="text-center">
                                {{ __('Yen currency') }}
                                {{ CommonHelper::priceFormat($pricesAndTaxes->tax_8_percent) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-none"></td>
                            <td colspan="1" class="preview__table-remarks border-none text-left p-0">
                                【 {{ __('Remarks') }} 】
                            </td>
                            <td colspan="2" class="left-border-none preview__table--border-double text-center">
                                {{ __('Grand total') }}
                            </td>
                            <td colspan="2" class="preview__table--border-double text-center">
                                {{ __('Yen currency') }}
                                {{ CommonHelper::priceFormat($pricesAndTaxes->overall_total) }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="preview__section">
        <table class="preview__table preview__table--no-border table">
            <tbody>
                @if ($form->remarks)
                    <tr>
                        <td class="preview__table-paragraph">
                            {!! nl2br(e($form->remarks)) ?? '' !!}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
