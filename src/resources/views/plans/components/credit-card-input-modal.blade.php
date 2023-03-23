<div class="modal fade" id="cc-input-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section loader --}}
            @include('components.section-loader', ['show' => false])

            <div class="modal-header">
                <h4 class="modal-title ">{{ __('Proceed to enter card information') }}</h4>
                <button class="btn-close js-cc-input-close" type="button" data-toggle="modal" data-target="#cc-input-modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <form action="{{ route('plans.subscription.save-payment-method') }}" method="POST" id="payment-form">
                    @csrf
                    <div class="form-group">
                        <div class="card-header">
                            <label for="card-element">
                                {{ __('Enter your credit card information') }}
                            </label>
                        </div>
                        <div class="card-body">
                            {{-- Error message --}}
                            <div class="alert-danger p-2 text-center mb-3 d-none">
                                <span id="card-errors" role="alert"></span>
                            </div>

                            <div class="cell cc credit-card" id="credit-card-payment">
                                @if($customer)
                                    <input type="hidden" id="customer" data-name="{{ $customer->name }}" value="{{ $customer->id }}" />
                                @endif
                                {{-- Credit card elements --}}
                                <div class="row">
                                    <div class="field card-number">
                                        <div id="cc-card-number" class="input empty"></div>
                                        <label for="cc-card-number" data-tid="elements_examples.form.card_number_label">
                                            {{ __('Card Number') }}
                                        </label>
                                        <div class="baseline"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="field half-width">
                                        <div id="cc-card-expiry" class="input empty"></div>
                                        <label for="cc-card-expiry" data-tid="elements_examples.form.card_expiry_label">
                                            {{ __('Expiration') }}
                                        </label>
                                        <div class="baseline"></div>
                                    </div>
                                    <div class="field half-width">
                                        <div id="cc-card-cvc" class="input empty"></div>
                                        <label for="cc-card-cvc"
                                            data-tid="elements_examples.form.card_cvc_label">CVC</label>
                                        <div class="baseline"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <p id="errorMessage" class="text-danger">
                        </p>
                        <button id="card-button" class="btn btn-primary" type="submit"
                            >{{ __('Change') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        let stripe = Stripe("{{ config('stripe.publishable_key') }}", {
            locale: "{{ config('stripe.locale') }}",
        });
        let elements = stripe.elements({
            fonts: [{
                cssSrc: 'https://fonts.googleapis.com/css?family=Source+Code+Pro',
            }],
            locale: "{{ config('stripe.locale') }}"
        });

        // Floating labels
        let inputs = document.querySelectorAll('.cell.cc.credit-card .input');
        Array.prototype.forEach.call(inputs, function(input) {
            input.addEventListener('focus', function() {
                input.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                input.classList.remove('focused');
            });
            input.addEventListener('keyup', function() {
                if (input.value.length === 0) {
                    input.classList.add('empty');
                } else {
                    input.classList.remove('empty');
                }
            });
        });

        let elementStyles = {
            base: {
                color: '#32325D',
                fontWeight: 500,
                fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
                fontSize: '16px',
                fontSmoothing: 'antialiased',

                '::placeholder': {
                    color: '#CFD7DF',
                },
                ':-webkit-autofill': {
                    color: '#E39F48',
                },
            },
            invalid: {
                '::placeholder': {
                    color: '#FFCCA5',
                },
            },
        };

        let elementClasses = {
            focus: 'focused',
            empty: 'empty',
            invalid: 'invalid',
        };

        let cardNumber = elements.create('cardNumber', {
            style: elementStyles,
            classes: elementClasses,
            showIcon: true,
        });

        let cardExpiry = elements.create('cardExpiry', {
            style: elementStyles,
            classes: elementClasses,
        });

        let cardCvc = elements.create('cardCvc', {
            style: elementStyles,
            classes: elementClasses,
        });

        cardNumber.mount('#cc-card-number');
        cardExpiry.mount('#cc-card-expiry');
        cardCvc.mount('#cc-card-cvc');

        const customer = document.getElementById('customer');
        const customerId = customer.value;
        const customerName = customer.dataset.name;
        let loader = $('.js-section-loader');

        /**
         * Handle form submission.
         */
        let form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            loader.removeClass('d-none');

            try {
                stripe.createPaymentMethod({
                    type: 'card',
                    card: cardNumber,
                    billing_details: {
                    name: customerName,
                    },
                })
                .then((response) => {
                    if (response.error) {
                        // Render error messages and stop loader
                        errorMessage(response.error.message);
                        loader.addClass('d-none');
                    } else {
                        attachPaymentMethod(response.paymentMethod);
                    }
                });
            } catch (error) {
                loader.addClass('d-none');
                $('#errorMessage').text('');
                $('#errorMessage').text(@json( __('Integration Error') ));
            }
        });

        /**
         * Handle on modal close
         */
        let modalCloseBtn = document.querySelector('.js-cc-input-close');
        modalCloseBtn.addEventListener('click', function(event) {
            $("#cc-input-modal").modal("hide");

            cardNumber.clear();
            cardExpiry.clear();
            cardCvc.clear();
            errorMessage(null);
            $('#errorMessage').text('');
        });

        /**
         * Handle payment process
         */
        function attachPaymentMethod(paymentMethod) {
            const url = $('#payment-form').attr('action');
            const data = {
                customer_id: customerId,
                payment_method: JSON.stringify(paymentMethod), 
            };

            // Call Payment process API
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url,
                data,
                beforeSend: function(request) {
                    let csrfToken = $('meta[name="csrf-token"]').attr('content');
                    let lang = $('html').attr('lang');
                    request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    request.setRequestHeader('Accept-Language', lang);
                },
                success: function(response) {
                    modalCloseBtn.click();
                    loader.addClass('d-none');
                    setAlert('success', response.data.message);
                    $("#cardLast4Digit").text(response.data.card);
                },
                error: function(error) {
                    loader.addClass('d-none');

                    if (error.hasOwnProperty('responseJSON')) {
                        return errorMessage(error.responseJSON.message);
                    }

                    return errorMessage(error.statusText);
                },
            });
        }

        /**
         * Render error messages
         */
        function errorMessage(message = null) {
            let error = $('#card-errors');
            let errorContainer = error.closest('.alert-danger');

            if (message !== null) {
                error.text(message);
                errorContainer.removeClass('d-none');
            } else {
                error.text('');
                errorContainer.addClass('d-none');
            }
        }

        /**
         * Reset card form fields
         */
        $('#cc-input-modal').on('hide.bs.modal', function (e) {
            elements.clear();
        })
    </script>
@endpush
