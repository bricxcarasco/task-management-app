@php
use App\Helpers\CommonHelper;
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link type="text/css" rel="stylesheet" href="css/pdf.css">
</head>

<body>
    <main>
        <section class="pt-20">
            <!-- Supplier name -->
            <div class="mb-20 bold">
                <span class="supplier-name supplier-name--underline supplier-name--md">{{ $form->supplier_name ?? $form->connected_supplier_name }}</span>
                <span class="supplier-name" style="font-size: 2.3rem;">{{ __('You') }}</span>
            </div>

            <!-- Heading -->
            <h1 class="heading">{{ __('Receipt') }}</h1>

            <!-- Data -->
            <table class="table text-17 bold pt-10">
                <tbody>
                    <tr class="table__row table__row--lg">
                        <td colspan="2" class="no-border border-bottom text-right">
                            <p class="text-20" style="letter-spacing: 40px; margin-right: -20px;">{{ __('Amount') }}</p>
                        </td>
                        <td colspan="6" class="text-24 no-border border-bottom text-center">
                            <p style="margin-left: -110px;">
                                {{ __('Yen currency') }}
                                {{ CommonHelper::priceFormat($form->price) }}
                                {{ __('Tax included') }}
                            </p>
                        </td>
                    </tr>
                    <tr class="table__row table__row--lg">
                        <td colspan="2" class="no-border text-right">
                            <span class="bold">{{ __('However') }}：</span>
                        </td>
                        <td colspan="6" class="no-border text-left">
                            {{ $form->title }}
                        </td>
                    </tr>
                    <tr class="table__row table__row--lg">
                        <td colspan="2" class="no-border text-right">
                            <span class="bold">{{ __('Reference') }}：</span>
                        </td>
                        <td colspan="6" class="no-border text-left">
                            {{ $form->refer_receipt_no }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table text-17 bold pt-10">
                <tr class="table__row table__row--lg">
                    <td class="no-border align-bottom" colspan="2" rowspan="5">
                        <div class="revenue-stamp">{{ __('Revenue stamp') }}</div>
                    </td>
                    <td colspan="2" class="no-border text-right">
                        <p class="pt-20" style="letter-spacing: 20px; margin-right: -20px;">{{ __('Date') }}</p>
                    </td>
                    <td colspan="4" class="no-border text-left">
                        <p class="pt-20">{{ __('Mentioned above, received') }}</p>
                    </td>
                </tr>
                <tr class="table__row table__row--lg">
                    <td colspan="2" class="no-border text-right">
                        <p class="pt-20">{{ __('Company name') }}</p>
                    </td>
                    <td colspan="4" class="no-border text-left">
                        <p class="pt-20">{{ $form->issuer_name }}</p>
                    </td>
                </tr>
                <tr class="table__row table__row--lg">
                    <td colspan="2" class="no-border text-right">
                        {{ __('Business Address') }}
                    </td>
                    <td colspan="4" class="no-border text-left">
                        {{ $form->issuer_address }}
                    </td>
                </tr>
                <tr class="table__row table__row--lg">
                    <td colspan="2" class="no-border text-right">
                        {{ __('Telephone No.') }}
                    </td>
                    <td colspan="4" class="no-border text-left">
                        {{ $form->issuer_tel }}
                    </td>
                </tr>
                <tr class="table__row table__row--lg">
                    <td colspan="2" class="no-border text-right">
                        {{ __('Office registration number') }}
                    </td>
                    <td colspan="4" class="no-border text-left">
                        {{ $form->issuer_business_number }}
                    </td>
                </tr>
            </table>
        </section>
    </main>
</body>

</html>
