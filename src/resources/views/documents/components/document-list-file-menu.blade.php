<div class="btn-group" role="group">
    <button type="button" class="btn btn-link px-3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="color-primary ai-more-vertical"></i>
    </button>
    <div class="dropdown-menu">
        <a href="#" class="dropdown-item">{{ __('Sharing Settings') }}</a>
        <button class="dropdown-item" data-action="#">
            {{ __('Delete') }}
        </button>
        <button class="dropdown-item" data-name="{{ $results->name }}" data-action="#">
            {{ __('Change Name') }}
        </button>
        <a href="#" class="dropdown-item">{{ __('Download') }}</a>
    </div>
</div>
<div class="clearfix"></div>
