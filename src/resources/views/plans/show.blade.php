@php
use App\Enums\EntityType;
use App\Enums\PaidPlan\PlanType;
use App\Enums\Form\ProductTaxDistinction;
use App\Helpers\CommonHelper;
@endphp

@extends('layouts.main')

@section('content')
    {{-- Flash Alert --}}
    @if(Session::get('alert'))
        @include('components.alert')
    @endif

    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 break-word">
        <div class="row">
            <div class="col-12 col-md-9 offset-md-3">
                <form action="{{ route('paid-plan.verify-incomplete-subscription') }}" method="POST">
                    @csrf

                    @if ($isDisplay)
                        <input type="hidden" name="stripe_customer_id" value="{{ $subscription->stripe_customer_id ?? '' }}" />
                        <input type="hidden" name="stripe_subscription_id" value="{{ $subscription->stripe_subscription_id ?? '' }}" />
                        <input type="hidden" name="stripe_client_secret" value="{{ $subscription->stripe_client_secret ?? '' }}" />
                    @endif

                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route("$serviceName.profile.introduction", $service->data->id) }}" class="btn btn-link">
                            {{ __('Back to Service Profile', 
                                [
                                'service' => strtoupper($serviceName),
                                ])
                            }}
                        </a>
                    </div>
                    <p class="text-center">
                        {{ $plan->name }}
                    </p>
                    <div class="position-relative
                        list--white
                        p-4
                        hoverable
                        mt-2
                        text-center
                        col-10
                        offset-1"
                    >
                        {{ $plan->description }}
                    </div>
                    <p class="mb-0 bg-dark-gray p-2 c-white mt-4">
                        {{ __('Functions list') }}
                    </p>
                    <table class="table bg-gray table-bordered">
                        <tbody>
                            @if ($plan->id === PlanType::RIO_FREE_PLAN && $plan->entity_type === EntityType::RIO)
                                <tr>
                                    <td class="px-4">{{ __('Document Management') }}</td>
                                    <td>{{ __('5GB') }}</td>
                                </tr>
                            @endif
    
                            @if ($plan->id === PlanType::RIO_LIGHT_PLAN && $plan->entity_type === EntityType::RIO)
                                <tr>
                                    <td class="px-4">{{ __('Electronic Contracts (CM Signature)') }}</td>
                                    <td>{{ __('Additional options, 1,000 yen per option') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Document Management') }}</td>
                                    <td>{{ __('7GB') }}</td>
                                </tr>
                            @endif
    
                            @if ($plan->id === PlanType::RIO_STANDARD_PLAN && $plan->entity_type === EntityType::RIO)
                                <tr>
                                    <td class="px-4">{{ __('Internet Shop (Classified service)') }}</td>
                                    <td>{{ __('Available') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Electronic Contracts (CM Signature)') }}</td>
                                    <td>{{ __('Additional options, 1,000 yen per option') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Document Management') }}</td>
                                    <td>{{ __('20GB') }}</td>
                                </tr>
                            @endif
    
                            @if ($plan->id === PlanType::RIO_PREMIUM_PLAN && $plan->entity_type === EntityType::RIO)
                                <tr>
                                    <td class="px-4">{{ __('Internet Shop (Classified service)') }}</td>
                                    <td>{{ __('Available') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Electronic Contracts (CM Signature)') }}</td>
                                    <td>{{ __('Additional options, 1,000 yen per option') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Document Management') }}</td>
                                    <td>{{ __('50GB') }}</td>
                                </tr>
                            @endif
    
                            @if ($plan->id === PlanType::NEO_FREE_PLAN && $plan->entity_type === EntityType::NEO)
                                <tr>
                                    <td class="px-4">{{ __('Number of registrations') }}</td>
                                    <td>{{ __('Up to 5 persons') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Internet Shop (Classified service)') }}</td>
                                    <td>{{ __('Additional options 5,000 yen/month') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Electronic Contracts (CM Signature)') }}</td>
                                    <td>{{ __('Additional options, 1,000 yen per option') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Document Management') }}</td>
                                    <td>{{ __('Additional option (10GB) 700 yen/month') }}</td>
                                </tr>
                            @endif
    
                            @if ($plan->id === PlanType::NEO_LIGHT_PLAN && $plan->entity_type === EntityType::NEO)
                                <tr>
                                    <td class="px-4">{{ __('Number of registrations') }}</td>
                                    <td>{{ __('Up to 50 persons') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Internet Shop (Classified service)') }}</td>
                                    <td>{{ __('Available') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Electronic Contracts (CM Signature)') }}</td>
                                    <td>{{ __('1 time/month') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Document Management') }}</td>
                                    <td>{{ __('50GB') }}</td>
                                </tr>
                            @endif
    
                            @if ($plan->id === PlanType::NEO_STANDARD_PLAN && $plan->entity_type === EntityType::NEO)
                                <tr>
                                    <td class="px-4">{{ __('Number of registrations') }}</td>
                                    <td>{{ __('Up to 100 persons') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Internet Shop (Classified service)') }}</td>
                                    <td>{{ __('Available') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Electronic Contracts (CM Signature)') }}</td>
                                    <td>{{ __('3 times/month') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Document Management') }}</td>
                                    <td>{{ __('100GB') }}</td>
                                </tr>
                            @endif
    
                            @if ($plan->id === PlanType::NEO_PREMIUM_PLAN && $plan->entity_type === EntityType::NEO)
                                <tr>
                                    <td class="px-4">{{ __('Number of registrations') }}</td>
                                    <td>{{ __('Up to 300 persons') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Internet Shop (Classified service)') }}</td>
                                    <td>{{ __('Available') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Electronic Contracts (CM Signature)') }}</td>
                                    <td>{{ __('10 times/month') }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4">{{ __('Document Management') }}</td>
                                    <td>{{ __('200GB') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <p class="mb-0 bg-dark-gray p-2 c-white">{{ __('charge') }}</p>
                    <p class="px-4 mt-3">{{ number_format($plan->price) }}円/月 {{ __('tax not included') }}</p>
                    <p class="mb-0 bg-dark-gray p-2 c-white">{{ __('Total monthly amount after plan contract') }}</p>
                    <table class="table bg-gray">
                        <tbody>
                            <tr>
                                <td class="px-4">{{ __('Basic Plan Rates') }}</td>
                                <td>￥{{ number_format($plan->price) }}/月</td>
                            </tr>
                            <tr>
                                <td class="px-4">(フリープラン)</td>
                                <td></td>
                            </tr>
                            <!-- <tr>
                                <td class="px-4">{{ __('Total Additional Options') }}</td>
                                <td>
                                    ￥8,000/月
                                </td>
                            </tr> -->
                            <tr class="border-top">
                                <td class="px-4">{{ __('Total Monthly Fee') }}</td>
                                <td>￥{{ number_format($plan->price) }}/月</td>
                            </tr>
                        </tbody>
                    </table>
                    @if ($isDisplay)    
                        <div class="mt-4 text-center mb-3">
                            <button
                                type="submit"
                                class="btn btn-primary btnRight"
                            >
                                {{ __('Proceed to Plan Change') }}
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
