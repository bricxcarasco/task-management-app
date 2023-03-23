<div class="col-12 col-md-4">
    <div class="card mb-2">
        <div class="card-body">
            <div class="card-text fs-sm my-3">
                <h6 class="m-0">{{ __('Product Name') }}</h6>
                <p class="m-0">{{ $product->content }}</p>
            </div>
            <div class="card-text fs-sm my-3">
                <h6 class="m-0">{{ __('Product Description') }}</h6>
                <p class="m-0">{{ $product->additional }}</p>
            </div>
            @if (!empty($product->information->reference_url))
                <div class="card-text fs-sm my-3">
                    <h6 class="m-0">{{ __('Reference URL') }}</h6>
                    <a class="openURLModal" data-reference-url="{{ $product->information->reference_url }}" href="#" class="m-0">
                        {{ $product->information->reference_url ?? '' }}
                    </a>
                </div>
            @endif
        </div>
        @if (!empty($product->information->image_link))
            <img class="card-img-bottom" src="{{ $product->information->image_link }}" onerror="this.src = '{{ asset('img/default-image.png') }}'">
        @endif
    </div>
</div>