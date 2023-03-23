@php
use App\Enums\Form\ProductTaxDistinction;
use App\Enums\Form\Types;
use App\Helpers\CommonHelper;
@endphp

<h1 class="heading">{{ __('Delivery slip')}}</h1>
<!-- First row -->
<table class="table table--sm">
  <tbody>
    <tr>
      <td width="55%">{{ $form->zipcode }}</td>
      <td rowspan="2" width="20%" class="pr-15 text-right">{{ __('Delivery Number') }}：</td>
      <td rowspan="2" width="25%" class="pl-10">{{ $form->form_no }}</td>
    </tr>
    <tr>
      <td>{{ $form->address }}</td>
    </tr>
  </tbody>
</table>

<!-- Second row -->
<table class="table table--sm">
  <tbody>
    <tr>
      <td rowspan="2" width="55%" class="pr-15 align-bottom" style="padding-top: 5px;">
        <span class="supplier-name supplier-name--underline bold">{{ $form->supplier_name ?? $form->connected_supplier_name }}</span>
        <span class="supplier-name bold">{{ __('You') }}</span>
      </td>
      <td width="33%" class="pl-20 align-bottom"> {{ $form->issuer_name }}</td>
      <td rowspan="2" width="12%" class="text-right align-middle">
        @if ($imagePath !== null)
          <img class="table__image" src="{{ $imagePath }}">
        @endif
      </td>
    </tr>
    <tr>
      <td class="pl-20">{{ $form->issuer_department_name }}</td>
    </tr>
    <tr>
      <td class="pr-15 align-top text-right" width="55%">{{ __('We will deliver the inquiry as follows.') }}</td>
      <td class="pl-20" colspan="2">{{ $form->issuer_address }}</td>
    </tr>
  </tbody>
</table>

<!-- Third row -->
<div class="table-container">
  <table class="table table--sm mt-10">
    <tbody>
      <tr>
        <td width="15%" class="pl-10 text-center border-bottom">{{ __('Subject') }}<span class="float-right">：</span></td>
        <td width="40%" class="pl-10 border-bottom ellipsis">{{ $form->title }}</td>
        <td width="20%" class="pr-15 text-right">ＴＥＬ：</td>
        <td width="25%" class="pl-10">{{ $form->issuer_tel }}</td>
      </tr>
      <tr>
        <td width="15%" class="pl-10 text-center border-bottom">{{ __('Offer amount') }}<span class="float-right">：</span></td>
        <td width="40%" class="bold text-center border-bottom">{{ __('Yen currency') }} {{ CommonHelper::priceFormat($pricesAndTaxes->overall_total) }} {{ __('Tax included') }}</td>
        <td width="20%" class="pr-15 text-right">ＦＡＸ：</td>
        <td width="25%" class="pl-10">{{ $form->issuer_fax }}</td>
      </tr>
      <tr>
        <td width="15%" class="pl-10 text-center border-bottom align-top">
          {{ __('Delivery location') }}<span class="float-right">：</span>
        </td>
        <td width="40%" class="pl-10 border-bottom">{{ $form->delivery_address }}</td>
        <td width="20%" class="pr-15 text-right align-top">{{ __('Office registration number') }}：</td>
        <td width="25%" class="pl-10 align-top">{{ $form->issuer_business_number }}</td>
      </tr>
      <tr>
        <td width="15%" class="pl-10 text-center border-bottom ">{{ __('Delivery Date') }}<span class="float-right">：</span></td>
        <td width="40%" class="pl-10 border-bottom">{{ $form->delivery_date  }}</td>
      </tr>
      <tr>
        <td width="15%" class="pl-10 text-center border-bottom ">{{ __('Delivery deadline') }} <span class="float-right">：</span></td>
        <td width="40%" class="pl-10 border-bottom">{{ $form->delivery_deadline }}</td>
      </tr>
      <!-- NOTE: Added empty rows to preserve layout for the stamp boxes for invoice -->
      <tr>
        <td colspan="4"></td>
      </tr>
      <tr>
        <td colspan="4"></td>
      </tr>
      <tr>
        <td colspan="4"></td>
      </tr>
      <tr>
        <td colspan="4"></td>
      </tr>
    </tbody>
  </table>

  <!-- Stamp -->
  <div class="stamp">
    <div class="stamp__item"></div>
    <div class="stamp__item"></div>
    <div class="stamp__item"></div>
  </div>
</div>

<!-- Divider -->
<div class="divider mt-20"></div>

<!-- Main table -->
<table class="table mt-20">
  <thead>
    <th width="10%">NO.</th>
    <th>{{ __('Product / service name') }}</th>
    <th>{{ __('Unit price') }}</th>
    <th width="12%">{{ __('Quantity') }}</th>
    <th>{{ __('Amount') }}</th>
    <th width="10%">{{ __('Tax') }}</th>
  </thead>
  <tbody>
    @if ($form->products->isNotEmpty())
    @foreach ($form->products as $key => $product)
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
    <tr>
      <td class="text-center">{{ $key+1 }} </td>
      <td class="text-left">{{ $product->name }}</td>
      <td class="text-right">{{ __('Yen currency') }}{{ $unitPrice }}</td>
      <td class="text-center">{{ $product->quantity }}</td>
      <td class="text-right">{{ __('Yen currency') }}{{ $amount }}</td>
      <td class="text-center">{{ $taxDistinction }}</td>
    </tr>
    @endforeach
    @endif
    @if (!empty($pricesAndTaxes))
    <tr>
      <td class="no-border"></td>
      <td colspan="2" class="text-center double-border">{{ __('Total') }}</td>
      <td class="double-border"></td>
      <td colspan="2" class="text-center double-border">{{ __('Yen currency') }}{{ CommonHelper::priceFormat($pricesAndTaxes->total_price) }}</td>
    </tr>
    <tr>
      <td colspan="2" class="no-border"></td>
      <td colspan="2" class="text-center no-border-left">{{ __('10% consumption tax') }}</td>
      <td colspan="2" class="text-center">{{ __('Yen currency') }}{{ CommonHelper::priceFormat($pricesAndTaxes->tax_10_percent) }}</td>
    </tr>
    <tr>
      <td colspan="2" class="no-border"></td>
      <td colspan="2" class="text-center no-border-left">{{ __('8% consumption tax') }}</td>
      <td colspan="2" class="text-center">{{ __('Yen currency') }}{{ CommonHelper::priceFormat($pricesAndTaxes->tax_8_percent) }}</td>
    </tr>
    <tr>
      <td colspan="2" class="no-border bold" style="letter-spacing: 10px; font-size: 1.6rem; padding-bottom: 0;">【{{ __('Delivery Location') }}】</td>
      <td colspan="2" class="text-center no-border-left double-border">{{ __('Grand total') }}</td>
      <td colspan="2" class="text-center double-border">{{ __('Yen currency') }}{{ CommonHelper::priceFormat($pricesAndTaxes->overall_total) }}</td>
    </tr>
    @endif
  </tbody>
</table>

<!-- Delivery Location -->
<div class="notes-wrapper mt-20">
  <p class="notes paragraph-content" style="word-wrap:break-word;">
    {!! nl2br(e($form->delivery_address)) ?? '' !!}
  </p>
</div>
<table class="table bold mt-20">
  <tbody>
    <tr>
      <td colspan="2" class="no-border bold text-center" style="padding-right: 35px; letter-spacing: 10px; font-size: 1.6rem; padding-bottom: 0;">【{{ __('Remarks') }}】</td>
      <td colspan="3" class="text-center no-border"></td>
      <td colspan="2" class="text-center no-border"></td>
    </tr>
  </tbody>
</table>
<!-- Remarks -->
<div class="notes-wrapper mt-20">
  <p class="notes paragraph-content" style="word-wrap:break-word;">
    {!! nl2br(e($form->remarks)) ?? '' !!}
  </p>
</div>