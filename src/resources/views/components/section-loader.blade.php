@php
    $show = $show ?? false;
@endphp

<div class="js-section-loader section-loader text-center {{ !$show ? ' d-none' : '' }}">
    <div class="spinner-border text-primary"></div>
</div>
