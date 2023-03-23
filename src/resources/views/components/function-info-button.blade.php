<div {{ $attributes->merge(['class' => 'js-function-info-container d-inline-block']) }}>
    <button type="button" class="btn btn-primary btn-sm rounded-circle py-1 px-2 fs-xs lh-1 js-function-info-modal d-none">?</button>

    <div class="js-function-info-content d-none">
        {{ $slot }}
    </div>
</div>