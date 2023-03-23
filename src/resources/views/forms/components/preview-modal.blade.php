@php
    use App\Enums\Form\ProductTaxDistinction;
    use App\Enums\Form\Types;
    use App\Helpers\CommonHelper;
@endphp

@push('css')
    <link rel="stylesheet" media="screen" href="{{ asset('css/form-preview.css') }}">
@endpush

<div class="modal fade" id="form-preview" tabindex="-1" role="dialog" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Form Preview') }}</h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="m-3 text-center">
                    {{-- Section loader --}}
                    @include('components.section-loader', ['show' => false])

                    <div id="frame">
                        {{-- Preview content --}}
                        @switch ($form->type)
                            @case(Types::RECEIPT)
                                @include('forms.components.preview-content-receipt')
                                @break 
                            @case(Types::INVOICE)
                                @include('forms.components.preview-content-invoice')
                                @break
                            @case(Types::PURCHASE_ORDER)
                                @include('forms.components.preview-content-purchase-order')
                                @break
                            @case(Types::DELIVERY_SLIP)
                                @include('forms.components.preview-content-delivery-slip')
                                @break
                            @default
                                @include('forms.components.preview-content-quotation')
                                @break
                        @endswitch
                    </div>

                    <img src="#" class="js-form-preview-img d-none" />
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="{{ mix('js/dist/imagecanvas-facade.js') }}" defer></script>

    <script>
        $(document).ready(function() {
            let frame = document.getElementById('frame');
            let frameSelector = $("#frame");
            let imgSelector = $('.js-form-preview-img');
            let modal = $("#form-preview");
            let loader = $('.js-section-loader');

            /**
             * Trigger on modal close
             */
            modal.on('hidden.bs.modal', function() {
                frameSelector.removeClass('d-none');
                imgSelector
                    .attr('src', '#')
                    .addClass('d-none');
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var imageSrc = document.getElementById("pdf-img").src;
            document.getElementById("pdf-img").src = imageSrc+ '?' + new Date().getTime();
        });
    </script>
@endpush
