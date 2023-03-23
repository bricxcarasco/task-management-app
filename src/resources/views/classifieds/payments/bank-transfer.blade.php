@php
use App\Enums\Classified\SettingTypes;
use App\Helpers\CommonHelper;
@endphp

@extends('layouts.main')

@section('content')
    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            <div class="col-12 col-md-9 offset-md-3">
                <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
                    <div class="position-relative">
                        <h3 class="py-3 mb-0 text-center">
                            {{ __('Payment Screen') }}
                            ({{ __('Bank transfer settlement') }})
                        </h3>
                    </div>
                    <div class="d-flex border align-items-center justify-content-between rounded-3  p-2">
                        <img class="img-rounded me-4" src="{{ $payment->main_photo }}" alt="Product image" width="60">
                        <p class="mb-2 flex-1 break-word">{{ $payment->product_title }}</p>
                    </div>
                    <table class="table table-bordered table--payment mt-4">
                        <tr>
                            <td>{{ __('Payment amount (Tax included / Shipping included)') }}</td>
                            <td class="text-right">¥{{ CommonHelper::priceFormat($payment->price) }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('Payment destination') }}</td>
                            <td class="text-right">{{ $payment->seller_name }}</td>
                        </tr>
                    </table>

                    <p>{{ __('Please transfer to the following account') }}</p>
                    @if (!empty($bankAccounts))
                        @foreach ($bankAccounts as $account)
                            <div class="bg-gray p-4">
                                {{ $account['bank'] }} {{ $account['branch'] }}
                                ({{ SettingTypes::getDescription($account['account_type']) }})
                                <br />
                                {{ $account['account_number'] }} <br />
                                カ）{{ $account['account_name'] }}
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
