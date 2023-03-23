<div class="btn-group" role="group">
    <button type="button" class="btn btn-link px-3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="color-primary ai-more-vertical"></i>
    </button>
    <div class="dropdown-menu">
        <!-- if($fileType == 'application/pdf') -->
            <a href="#" class="dropdown-item">{{ __('Sharing Settings') }}</a>
            <a href="#" class="dropdown-item">{{ __('Get Shared Link') }}</a>
            <button class="dropdown-item" data-action="#">
                {{ __('Delete') }}
            </button>
            <button class="dropdown-item" data-name="{{ $results->name }}" data-action="#">
                {{ __('Change Name') }}
            </button>
        <!-- endif -->
        <a href="#" class="dropdown-item">{{ __('Download') }}</a>
    </div>
</div>
<div class="clearfix"></div>
