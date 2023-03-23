@extends('layouts.main')

{{-- Flash Alert --}}
@include('components.alert')

@section('content')

    @if ($subscription)
        @include('paid-plan.modals.payment-method-modal')
        @include('paid-plan.modals.credit-card-input-modal')
    @endif

    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            <div class="col-12 col-md-9 offset-md-3">
                <div class="d-flex flex-column h-100 rounded-3 ">
                    <div class="position-relative">
                        <div class="tab-content mt-2">
                            <div class="tab-pane fade active show">
                                <div class="position-relative">   
                                    <a href="{{ route('plans.show', $plan->id) }}">
                                        <i class="ai-arrow-left message__back"> {{ __('戻る') }}</i>
                                    </a>                                               
                                    <h3 class="py-3 mb-0 text-center ">
                                        {{ __('プラン変更の確認') }}
                                    </h3>                                        
                                </div>                
                                <!-- Confirmation -->
                                <div class="container position-relative zindex-5 mb-md-3 home--height">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Current Plan') }}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $plan->name }}</td>
                                                        <td class="w-50">￥{{ number_format($plan->price) }}/月</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                
                                        {{-- <div class="table-responsive">
                                            <table class="table table-bordered text-dark">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('New Plan') }}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>ライトプラン</td>
                                                        <td class="w-50">￥10,000/月</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div> --}}
                                
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-active">
                                                    <tr class="table-dark">
                                                        <th colspan="2">
                                                            {{ __('Monthly total amount after option change') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="table-primary">
                                                        <td>{{ __('Basic Plan Rates') }} ({{ $plan->name }})</td>
                                                        <td class="w-50">￥{{ number_format($plan->price) }}/月</td>
                                                    </tr>
                                                    {{-- <tr class="table-primary">
                                                        <td>{{ __('Total Additional Options') }}</td>
                                                        <td class="w-50">￥8,000/月</td>
                                                    </tr> --}}
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-primary">
                                                        <td>{{ __('Total Monthly Fee') }}</td>
                                                        <td class="w-50">￥{{ number_format($plan->price) }}/月</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                
                                        <div class="text-center">
                                            <div class="border border-danger border-4 mb-3 p-2">
                                                <h5>
                                                    {{ __('Notes on Option Changes') }}
                                                </h5>
                                                <p class="card-text">
                                                    {{ __('Upgrade paragraph - Notes on Option Changes') }}<br/><br/>
                                                    {{ __('Downgrade paragraph - Notes on Option Changes') }}<br/><br/>
                                                    {{ __('Retain subscription paragraph -  Notes on Option Changes') }}<br/>
                                                </p>
                                            </div>
                                            <button
                                                type="button"
                                                class="btn btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#paid-plan-payment-modal"
                                            >
                                                {{ __('Check the precautions') }}<br />
                                                {{ __('Confirm Changes') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
@endsection