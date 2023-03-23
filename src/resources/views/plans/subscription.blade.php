@extends('layouts.main')

@section('content')

    {{-- Flash Alert --}}
    @include('components.alert')

    {{-- Modals --}}
    @include('plans.components.delete-confirmation-modal')
    @include('plans.components.credit-card-input-modal')

    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 break-word">
        <div class="row">
            <div class="col-12 col-md-9 offset-md-3">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ route("$serviceName.profile.introduction", $service->data->id) }}" class="btn btn-link">
                        {{ __('Back to Service Profile', 
                            [
                            'service' => strtoupper($serviceName),
                            ])
                        }}
                    </a>
                </div>
                <p class="text-center mt-3">
                    {{ __('Service Confirmation and change of contract information', 
                        [
                        'service' => strtoupper($serviceName),
                        ])
                    }}
                </p>
                <p class="mb-0 bg-dark-gray p-2 c-white mt-3">{{ __('Current Monthly Total') }}</p>
                <table class="table bg-gray">
                    <tbody>
                        <tr>
                            <td class="px-3">{{ __('Basic Plan Rates') }}</td>
                            <td>￥{{ number_format($subscription->price) }}/月</td>
                        </tr>
                        <tr>
                            <td class="px-4">{{ $subscription->name }}</td>
                            <td></td>
                        </tr>
                        {{-- <tr>
                            <td class="px-3">{{ __('Total Additional Options') }}</td>
                            <td>￥8,000/月</td>
                        </tr> --}}
                        <tr class="border-top">
                            <td class="px-3">{{ __('Total Monthly Fee') }}</td>
                            <td>￥{{ number_format($subscription->price) }}/月</td>
                        </tr>
                    </tbody>
                </table>
                <p class="bg-dark-gray p-2 c-white">{{ __('Under Contract Plan') }}</p>
                <div class="m-3 d-flex align-items-center justify-content-between ">
                    <span>
                        {{ $subscription->name }}
                    </span>
                    <div>
                        <a href="{{ route('plans.index') }}">{{ __('Change') }}</a>
                    </div>
                </div>
                {{-- <p class="mb-0 bg-dark-gray p-2 c-white mt-3">{{ __('Additional Options') }}</p>
                <table class="table bg-gray">
                    <tbody>
                        <tr>
                            <td class="px-3">文書管理オプション</td>
                            <td>追加済み（30GB）</td>
                            <td><a href="#">{{ __('Change') }}</a></td>
                        </tr>
                        <tr>
                            <td class="px-3">増員オプション</td>
                            <td>追加済み（10枠）</td>
                            <td><a href="#">{{ __('Change') }}</a></td>
                        </tr>
                        <tr>
                            <td class="px-3">ネットショップオプション</td>
                            <td>追加済み</td>
                            <td><a href="#">{{ __('Change') }}</a></td>
                        </tr>
                        <tr>
                            <td class="px-3">請求書オプション</td>
                            <td>未追加</td>
                            <td><a href="#">{{ __('Change') }}</a></td>
                        </tr>
                    </tbody>
                </table> --}}
                <p class="mb-0 bg-dark-gray p-2 c-white">{{ __('Payment Information for Plans and Options') }}</p>
                @if(!$cardPaymentMethod || !$cardPaymentMethod->data)
                    <div>
                        <div class="px-3 mt-2">{{ __('Not registered') }}</div>
                        <div class="px-3 mt-2">{{ __('Plan payment information is registered at the time of signing up for a paid plan') }} </div>
                    </div>
                @else
                    @foreach($cardPaymentMethod->data as $paymentMethod)
                        <div class="m-3 d-flex align-items-center justify-content-between">
                            <span>
                                クレジットカード：****
                                <span id="cardLast4Digit"> {{$paymentMethod->card->last4}} </span>
                            </span>
                            <div>
                                <a class="m-3" data-target="#cc-input-modal" data-toggle="modal" href="#">変更</a>
                                <a
                                    id="delete-payment-method"
                                    data-action="{{ route('plans.subscription.delete-payment-method') }}"
                                    href="#"
                                >
                                    削除
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            /**
             * Handle on delete payment method
             */
            $('#delete-payment-method').on('click', function(event) {
                event.preventDefault();
                let formSelector = $('#delete-confirm-form');

                // Set form action
                formSelector.attr('action', $(this).data('action'));

                // Show modal
                $('#delete-confirmation-modal').modal('show');
            });
        });
    </script>
@endpush
