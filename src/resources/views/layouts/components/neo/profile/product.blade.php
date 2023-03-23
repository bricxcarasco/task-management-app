<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title">{{ $product->content }}</h5>
        </div>
        <p class="card-text fs-sm">
            {{ $product->additional }}
            <br><br>
            <a class="openURLModal" data-reference-url="{{ $product->reference_url }}" href="#" class="m-0">
                {{ $product->reference_url }}
            </a>
        </p>
    </div>
    @if (!empty($product->image_link))
        <img class="card-img-bottom" src="{{ asset($product->image_link) }}" alt="{{ $product->content }}">
    @endif
</div>