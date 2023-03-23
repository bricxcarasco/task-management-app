<div class="btn-group" role="group">
    <button type="button" class="btn btn-link px-3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="color-primary ai-more-vertical"></i>
    </button>
    <div class="dropdown-menu">
        <button class="dropdown-item js-delete-group-btn"
            data-action="{{ route('connection.groups.delete', ['group' => $connection->group->id]) }}">{{ __('Delete') }}</button>
        </button>
        <a href="{{ route('connection.groups.invite-members', ['group' => $connection->group->id]) }}" class="dropdown-item">{{ __('Member invitation') }}</a>
        <button class="dropdown-item js-update-group-btn" data-name="{{ $connection->group->group_name }}"
            data-action="{{ route('connection.groups.update', ['group' => $connection->group->id]) }}">{{ __('Group name change') }}</button>
        <a href="{{ route('neo.registration.index', ['group' => $connection->group->id]) }}" class="dropdown-item">{{ __('Change to NEO') }}</a>
    </div>
</div>
<div class="clearfix"></div>
