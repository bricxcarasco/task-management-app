<div class="modal fade" id="privacy" tabindex="-1" style="display: none;" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title ">{{ __('Management Menu') }}</h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="{{ route('rio.profile.edit') }}" class="d-block c-primary">{{ __('Profile Settings') }}</a>
                    </li>
                    <li class="list-group-item">
                        <a href="{{ route('rio.privacy.edit') }}" class="d-block c-primary">{{ __('Privacy Settings') }}</a>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <a href="{{ route('rio.profile.invitation-list') }}" class="d-block c-primary">{{ __('Invitation management') }}</a>
                        <span class="badge bg-danger">{{ $invitationCount }}</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <a href="{{ route('plans.subscription') }}" class="d-block c-primary">{{ __('Confirmation and change of contract information') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
