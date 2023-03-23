@php
use App\Enums\Form\ProductTaxDistinction;
use App\Helpers\CommonHelper;

@endphp

@extends('layouts.main')

@section('content')
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
                <p class="text-center">
                  {{ __('Service Plan List', 
                    [
                      'service' => strtoupper($serviceName),
                    ])
                  }}
                </p>
                  <div class="mt-4">
                    <ul class="form__lists list-unstyled">
                      @foreach($plans as $plan)
                      <li
                        class="
                          position-relative
                          list--white
                          p-3
                          hoverable
                          header_rio
                          mt-2
                        "
                      >
                        <a href="{{ route('plans.show', $plan->id) }}" class="text-dark">
                          <div
                            class="d-flex align-items-center justify-content-between"
                          >
                            <span class="me-2">
                              <strong>
                                {{ $plan->name }}
                              </strong>
                            </span>
                            <div>
                              <span class="me-2">
                                  @if (!empty($plan->price))
                                    {{ number_format($plan->price) }} {{ __('Yen per month') }}
                                  @else
                                    {{ __('Free Price') }}
                                  @endif
                              </span>
                            </div>
                          </div>
                          <div
                            class="
                              d-flex
                              align-items-center
                              justify-content-center
                              mt-3
                            "
                          >
                            <div class="flex-1">
                              <span>
                                  {{ $plan->description }}
                              </span>
                            </div>
                          </div>
                        </a>
                        </li>
                      @endforeach
                      
                        {{-- <li
                          class="
                            position-relative
                            list--white
                            p-3
                            hoverable
                            header_rio
                            mt-2
                          "
                        >
                          <a href="{{ route('plans.show') }}" class="text-dark">
                            <div
                              class="d-flex align-items-center justify-content-between"
                            >
                              <span class="me-2">
                                <strong>ライトプラン</strong>
                              </span>
                              <div>
                                <span class="me-2">
                                    10,000円/月
                                </span>
                              </div>
                            </div>
                            <div
                              class="
                                d-flex
                                align-items-center
                                justify-content-center
                                mt-3
                              "
                            >
                              <div class="flex-1">
                                <span>
                                    標準でネットショップも利用できるお手軽プラン
                                </span>
                              </div>
                            </div>
                          </a>
                        </li>
                        <li
                          class="
                            position-relative
                            list--white
                            p-3
                            hoverable
                            header_rio
                            mt-2
                          "
                        >
                          <a href="{{ route('plans.show') }}" class="text-dark">
                            <div
                              class="d-flex align-items-center justify-content-between"
                            >
                              <span class="me-2">
                                <strong>スタンダードプラン</strong>
                              </span>
                              <div>
                                <span class="me-2">
                                    20,000円/月
                                </span>
                              </div>
                            </div>
                            <div
                              class="
                                d-flex
                                align-items-center
                                justify-content-center
                                mt-3
                              "
                            >
                              <div class="flex-1">
                                <span>
                                    十分な機能と容量を備えた標準プラン
                                </span>
                              </div>
                            </div>
                          </a>
                        </li>
                        <li
                          class="
                            position-relative
                            list--white
                            p-3
                            hoverable
                            header_rio
                            mt-2
                          "
                        >
                          <a href="{{ route('plans.show') }}" class="text-dark">
                            <div
                              class="d-flex align-items-center justify-content-between"
                            >
                              <span class="me-2">
                                <strong>プレミアムプラン</strong>
                              </span>
                              <div>
                                <span class="me-2">
                                    30,000円/月
                                </span>
                              </div>
                            </div>
                            <div
                              class="
                                d-flex
                                align-items-center
                                justify-content-center
                                mt-3
                              "
                            >
                              <div class="flex-1">
                                <span>
                                    大規模なチームでの本格的な利用におすすめ
                                </span>
                              </div>
                            </div>
                          </a>
                        </li> --}}
                      </ul>
                  </div>
                <div class="d-flex justify-content-center mt-5 mb-3">
                  <a class="text-center" id="plan-page" href="#" >
                      {{ __('Click here for a list of plan features') }}
                  </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            /**
             * Show plan page in new tab
             */
            $('#plan-page').unbind().bind('click', function(event) {
                event.preventDefault();
                window.open('https://hero.ne.jp/plan', '_blank');
            });
        });
    </script>
@endpush
