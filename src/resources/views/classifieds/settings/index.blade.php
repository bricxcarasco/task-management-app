@php
use App\Enums\Classified\CardPaymentStatus;
@endphp

@extends('layouts.main')

@section('content')
    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            {{-- Flash message --}}
            @include('components.alert')

            {{-- Loader --}}
            @include('components.section-loader')

            <div class="col-12 col-md-9 offset-md-3">
                <div class="d-flex flex-column pb-4 pb-md-0 rounded-3 ">
                    <div class="position-relative">
                        <h3 class="py-3 mb-0 text-center">
                            {{ __('Service Owned Classifieds', ['name' => $serviceName]) }}
                        </h3>
                    </div>
                    <div>
                        <a href="{{ route('classifieds.sales.index') }}" class="btn btn-link">
                            <i class="ai-arrow-left me-2"></i>
                            {{ __('Back to product list') }}
                        </a>
                    </div>
                    <h5 class="text-center">
                        {{ __('Receiving account setting status') }}
                    </h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-1 py-1 list--white text-center fs-sm">
                        @if ($cardPaymentStatus === CardPaymentStatus::COMPLETED)
                            {{ __('Card payment receiving account setting (Stripe cooperation)') }}：
                            <a href="#" class="btn btn-link p-0" data-toggle="modal" type="button" data-bs-toggle="modal"
                                data-bs-target="#unset-confirmation-modal">{{ __('Set') }}</a>
                        @elseif ($cardPaymentStatus === CardPaymentStatus::RESTRICTED)
                            {{ __('Card payment receiving account setting (Stripe cooperation)') }}：
                            <span class="btn btn-link no-hover">{{ __('Restricted') }}</span>
                        @elseif ($cardPaymentStatus === CardPaymentStatus::PENDING)
                            {{ __('Card payment receiving account setting (Stripe cooperation)') }}：
                            <span class="btn btn-link no-hover">{{ __('Pending') }}</span>
                        @else
                            <div class="d-inline-flex">
                                {{ __('Card payment receiving account setting (Stripe cooperation)') }}：
                                <form method="POST" action="{{ route('classifieds.settings.save-card-payment') }}"
                                    novalidate>
                                    @csrf
                                    <button class="btn btn-link p-0 js-card-payment-submit" type="submit">
                                        {{ __('Not set') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </li>
                    <li class="list-group-item px-1 py-1 list--white text-center fs-sm">
                        {{ __('Account transfer settlement Receiving account setting') }}：
                        <a href="{{ route('classifieds.settings.account-transfer') }}" class="btn btn-link p-0">
                            @if ($isSetAccountTransfer)
                                {{ __('Set') }}
                            @else
                                {{ __('Not set') }}
                            @endif
                        </a>
                    </li>
                </ul>
                <h5 class="mt-4 text-center">{{ __('About receiving account setting') }}</h5>
                <p>{{ __('Make the necessary settings') }}</p>
                <ul>
                    <li class="mb-4">
                        {{ __('Settings Item 1') }}<br><br>
                        {{ __('Settings Paragraph 1') }}
                        <a href="https://dashboard.stripe.com/register">{{ __('This page') }}</a>
                        {{ __('Settings Paragraph 2') }}
                    </li>
                    <li class="mb-4">
                        {{ __('Settings Item 2') }}<br><br>
                        {{ __('Settings Paragraph 3') }}
                    </li>
                    <li class="mb-4">
                        {{ __('Settings Item 3') }}<br><br>
                        {{ __('Settings Paragraph 4') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('components.unset-confirmation-modal')
    @include('classifieds.components.pending-verification-modal')
    @include('classifieds.components.revoked-account-modal')
    @include('classifieds.components.missing-information-modal', [
        'requirements' => $requirements,
    ])
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            let requirements = @json($requirements);
            let stripeInfo = @json($stripeInfo);
            let hasRequirements = requirements.length > 0;
            let missingInfoModal = $('#missing-info-modal');
            let pendingVerificationModal = $('#pending-verification-modal');
            let revokedAccountModal = $('#revoked-account-modal');

            $('.js-card-payment-submit').unbind().bind('click', function(event) {
                const loader = $('.js-section-loader');

                // Start loader
                loader.removeClass('d-none');
            });

            // Trigger open missing information modal
            if (stripeInfo !== null && stripeInfo.is_revoked) {
                revokedAccountModal.modal('show');

                return;
            }

            // Trigger open missing information modal
            if (hasRequirements) {
                missingInfoModal.modal('show');

                return;
            }

            // Trigger open pending verification modal
            if (stripeInfo !== null && stripeInfo.is_pending) {
                pendingVerificationModal.modal('show');
            }
        });
    </script>
@endpush
